<?php

namespace RestApiBundle\Services\OpenApi;

use RestApiBundle;
use Symfony;
use cebe\openapi\spec as OpenApi;
use Symfony\Component\Yaml\Yaml;
use function array_merge;
use function array_values;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;
use function strtolower;

class SpecificationGenerator
{
    /**
     * @var RestApiBundle\Services\OpenApi\RequestModelHelper
     */
    private $requestModelHelper;

    /**
     * @var RestApiBundle\Services\OpenApi\ResponseCollector
     */
    private $responseCollector;

    public function __construct(
        RestApiBundle\Services\OpenApi\RequestModelHelper $requestModelHelper,
        RestApiBundle\Services\OpenApi\ResponseCollector $responseCollector
    ) {
        $this->requestModelHelper = $requestModelHelper;
        $this->responseCollector = $responseCollector;
    }

    /**
     * @param RestApiBundle\DTO\OpenApi\EndpointData[] $endpoints
     *
     * @return string
     */
    public function generateYaml(array $endpoints): string
    {
        $data = $this
            ->generateSpecification($endpoints)
            ->getSerializableData();

        return Yaml::dump($data, 256, 4, Yaml::DUMP_OBJECT_AS_MAP);
    }

    /**
     * @param RestApiBundle\DTO\OpenApi\EndpointData[] $endpoints
     *
     * @return string
     */
    public function generateJson(array $endpoints): string
    {
        $data = $this
            ->generateSpecification($endpoints)
            ->getSerializableData();

        $result = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException(json_last_error_msg());
        }

        return $result;
    }

    /**
     * @param RestApiBundle\DTO\OpenApi\EndpointData[] $endpointDataItems
     *
     * @return OpenApi\OpenApi
     */
    private function generateSpecification(array $endpointDataItems): OpenApi\OpenApi
    {
        $root = new OpenApi\OpenApi([
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Open API Specification',
                'version' => '1.0.0',
            ],
            'paths' => [],
        ]);

        $tags = [];

        foreach ($endpointDataItems as $routeData) {
            foreach ($routeData->getTags() as $tagName) {
                if (isset($tags[$tagName])) {
                    continue;
                }

                $tags[$tagName] = new OpenApi\Tag([
                    'name' => $tagName,
                ]);
            }

            $returnType = $this->responseCollector->resolveSchemaByResponse($routeData->getResponse());

            $responses = new OpenApi\Responses([]);

            if ($returnType->getNullable()) {
                $responses->addResponse('204', new OpenApi\Response(['description' => 'Success response with empty body']));
            }

            if (!$returnType instanceof RestApiBundle\DTO\OpenApi\Schema\NullType) {
                $responses->addResponse('200', new OpenApi\Response([
                    'description' => 'Success response with body',
                    'content' => [
                        'application/json' => [
                            'schema' => $this->convertSchemaType($returnType)
                        ]
                    ]
                ]));
            }

            $pathParameters = [];

            foreach ($routeData->getRoutePathParameters() as $pathParameter) {
                $pathParameters[] = $this->createParameter('path', $pathParameter->getName(), $pathParameter->getSchema());
            }

            $pathItem = $root->paths->getPath($routeData->getRoutePath());
            if (!$pathItem) {
                $pathItem = new OpenApi\PathItem([]);
            }

            foreach ($routeData->getRouteMethods() as $method) {
                $isHttpGetMethod = $method === 'GET';
                $method = strtolower($method);

                $operation = new OpenApi\Operation([
                    'summary' => $routeData->getTitle(),
                    'responses' => $responses,
                ]);

                if ($routeData->getDescription()) {
                    $operation->description = $routeData->getDescription();
                }

                $queryParameters = [];
                if ($routeData->getRequest() && $isHttpGetMethod) {
                    $queryParameters = $this->convertRequestModelToParameters($routeData->getRequest());
                } elseif ($routeData->getRequest() && !$isHttpGetMethod) {
                    $operation->requestBody = $this->convertRequestModelToRequestBody($routeData->getRequest());
                }

                if ($pathParameters || $queryParameters) {
                    $operation->parameters = array_merge($pathParameters, $queryParameters);
                }

                if ($routeData->getTags()) {
                    $operation->tags = $routeData->getTags();
                }

                $pathItem->{$method} = $operation;
            }

            $root->paths->addPath($routeData->getRoutePath(), $pathItem);
        }

        $root->tags = array_values($tags);

        return $root;
    }

    private function convertRequestModelToRequestBody(RestApiBundle\DTO\OpenApi\Schema\ClassType $classType): OpenApi\RequestBody
    {
        $requestModelSchema = $this->requestModelHelper->getSchemaByClass($classType->getClass());

        return new OpenApi\RequestBody([
            'description' => 'Request body',
            'required' => !$classType->getNullable(),
            'content' => [
                'application/json' => [
                    'schema' => $this->convertSchemaType($requestModelSchema),
                ]
            ]
        ]);
    }

    /**
     * @return OpenApi\Parameter[]
     */
    private function convertRequestModelToParameters(RestApiBundle\DTO\OpenApi\Schema\ClassType $classType): array
    {
        $objectType = $this->requestModelHelper->getSchemaByClass($classType->getClass());

        $result = [];

        foreach ($objectType->getProperties() as $name => $property) {
            $result[] = $this->createParameter('query', $name, $property);
        }

        return $result;
    }

    private function createParameter(string $type, string $name, RestApiBundle\DTO\OpenApi\Schema\SchemaTypeInterface $schema): OpenApi\Parameter
    {
        $data = [
            'in' => $type,
            'name' => $name,
            'required' => !$schema->getNullable(),
        ];

        // Swagger UI does not show schema description in parameters
        if ($schema instanceof RestApiBundle\DTO\OpenApi\Schema\DescriptionAwareInterface && $schema->getDescription()) {
            $data['description'] = $schema->getDescription();
            $data['schema'] = $this->convertSchemaType($schema);
        } else {
            $data['schema'] = $this->convertSchemaType($schema);
        }

        return new OpenApi\Parameter($data);
    }

    private function convertSchemaType(RestApiBundle\DTO\OpenApi\Schema\SchemaTypeInterface $schemaType): OpenApi\Schema
    {
        if ($schemaType instanceof RestApiBundle\DTO\OpenApi\Schema\ObjectType) {
            $result = $this->convertObjectType($schemaType);
        } elseif ($schemaType instanceof RestApiBundle\DTO\OpenApi\Schema\ArrayType) {
            $result = $this->convertArrayType($schemaType);
        } elseif ($schemaType instanceof RestApiBundle\DTO\OpenApi\Schema\ScalarInterface) {
            $result = $this->convertScalarType($schemaType);
        } elseif ($schemaType instanceof RestApiBundle\DTO\OpenApi\Schema\DateTimeType) {
            $result = $this->convertDateTimeType($schemaType);
        } elseif ($schemaType instanceof RestApiBundle\DTO\OpenApi\Schema\DateType) {
            $result = $this->convertDateType($schemaType);
        } else {
            throw new \InvalidArgumentException();
        }

        if ($schemaType instanceof RestApiBundle\DTO\OpenApi\Schema\ValidationAwareInterface) {
            foreach ($schemaType->getConstraints() as $constraint) {
                if ($constraint instanceof Symfony\Component\Validator\Constraints\Range) {
                    if ($constraint->min !== null) {
                        $result->minimum = $constraint->min;
                    }

                    if ($constraint->max !== null) {
                        $result->maximum = $constraint->max;
                    }
                } elseif ($constraint instanceof Symfony\Component\Validator\Constraints\Choice) {
                    if ($constraint->choices) {
                        $choices = $constraint->choices;
                    } elseif ($constraint->callback) {
                        $callback = $constraint->callback;
                        $choices = $callback();
                    } else {
                        throw new \InvalidArgumentException();
                    }

                    $result->enum = $choices;
                } elseif ($constraint instanceof Symfony\Component\Validator\Constraints\Count) {
                    if ($constraint->min !== null) {
                        $result->minItems = $constraint->min;
                    }

                    if ($constraint->max !== null) {
                        $result->maxItems = $constraint->max;
                    }
                } elseif ($constraint instanceof Symfony\Component\Validator\Constraints\Length) {
                    if ($constraint->min !== null) {
                        $result->minLength = $constraint->min;
                    }

                    if ($constraint->max !== null) {
                        $result->maxLength = $constraint->max;
                    }
                }
            }
        }

        return $result;
    }

    private function convertScalarType(RestApiBundle\DTO\OpenApi\Schema\ScalarInterface $scalarType): OpenApi\Schema
    {
        if ($scalarType instanceof RestApiBundle\DTO\OpenApi\Schema\StringType) {
            $result = $this->convertStringType($scalarType);
        } elseif ($scalarType instanceof RestApiBundle\DTO\OpenApi\Schema\IntegerType) {
            $result = $this->convertIntegerType($scalarType);
        } elseif ($scalarType instanceof RestApiBundle\DTO\OpenApi\Schema\FloatType) {
            $result = $this->convertFloatType($scalarType);
        } elseif ($scalarType instanceof RestApiBundle\DTO\OpenApi\Schema\BooleanType) {
            $result = $this->convertBooleanType($scalarType);
        } else {
            throw new \InvalidArgumentException();
        }

        return $result;
    }

    private function convertObjectType(RestApiBundle\DTO\OpenApi\Schema\ObjectType $objectType): OpenApi\Schema
    {
        $properties = [];

        foreach ($objectType->getProperties() as $key => $propertyType) {
            $properties[$key] = $this->convertSchemaType($propertyType);
        }

        return new OpenApi\Schema([
            'type' => OpenApi\Type::OBJECT,
            'nullable' => $objectType->getNullable(),
            'properties' => $properties,
        ]);
    }

    private function convertArrayType(RestApiBundle\DTO\OpenApi\Schema\ArrayType $arrayType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::ARRAY,
            'nullable' => $arrayType->getNullable(),
            'items' => $this->convertSchemaType($arrayType->getInnerType()),
        ]);
    }

    private function convertStringType(RestApiBundle\DTO\OpenApi\Schema\StringType $stringType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::STRING,
            'nullable' => $stringType->getNullable(),
        ]);
    }

    private function convertIntegerType(RestApiBundle\DTO\OpenApi\Schema\IntegerType $integerType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::INTEGER,
            'nullable' => $integerType->getNullable(),
        ]);
    }

    private function convertFloatType(RestApiBundle\DTO\OpenApi\Schema\FloatType $floatType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::NUMBER,
            'format' => 'double',
            'nullable' => $floatType->getNullable(),
        ]);
    }

    private function convertBooleanType(RestApiBundle\DTO\OpenApi\Schema\BooleanType $booleanType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::BOOLEAN,
            'nullable' => $booleanType->getNullable(),
        ]);
    }

    private function convertDateTimeType(RestApiBundle\DTO\OpenApi\Schema\DateTimeType $dateTimeType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::STRING,
            'format' => 'date-time',
            'nullable' => $dateTimeType->getNullable(),
        ]);
    }

    private function convertDateType(RestApiBundle\DTO\OpenApi\Schema\DateType $dateType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::STRING,
            'format' => 'date',
            'nullable' => $dateType->getNullable(),
        ]);
    }
}
