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

        foreach ($endpointDataItems as $endpointData) {
            foreach ($endpointData->getEndpointAnnotation()->tags as $tagName) {
                if (isset($tags[$tagName])) {
                    continue;
                }

                $tags[$tagName] = new OpenApi\Tag([
                    'name' => $tagName,
                ]);
            }

            $returnType = $endpointData->getReturnType();

            $responses = new OpenApi\Responses([]);

            if ($returnType->getNullable()) {
                $responses->addResponse('204', new OpenApi\Response(['description' => 'Success response with empty body']));
            }

            if (!$returnType instanceof RestApiBundle\DTO\OpenApi\Schema\NullType) {
                $responses->addResponse('200', new OpenApi\Response([
                    'description' => 'Success response with body',
                    'content' => [
                        'application/json' => [
                            'schema' => $this->convertInnerTypeToOpenApiSchema($returnType)
                        ]
                    ]
                ]));
            }

            $pathParameters = [];

            foreach ($endpointData->getActionParameters() as $pathParameter) {
                $pathParameters[] = $this->createParameter('path', $pathParameter->getName(), $pathParameter->getSchema());
            }

            $pathItem = $root->paths->getPath($endpointData->getPath());
            if (!$pathItem) {
                $pathItem = new OpenApi\PathItem([]);
            }

            foreach ($endpointData->getMethods() as $method) {
                $isHttpGetMethod = $method === 'GET';
                $method = strtolower($method);

                $operation = new OpenApi\Operation([
                    'summary' => $endpointData->getTitle(),
                    'responses' => $responses,
                ]);

                if ($endpointData->getDescription()) {
                    $operation->description = $endpointData->getDescription();
                }

                $queryParameters = [];
                if ($endpointData->getRequestModel() && $isHttpGetMethod) {
                    $queryParameters = $this->convertRequestModelToParameters($endpointData->getRequestModel());
                } elseif ($endpointData->getRequestModel() && !$isHttpGetMethod) {
                    $operation->requestBody = $this->convertRequestModelToRequestBody($endpointData->getRequestModel());
                }

                if ($pathParameters || $queryParameters) {
                    $operation->parameters = array_merge($pathParameters, $queryParameters);
                }

                if ($endpointData->getTags()) {
                    $operation->tags = $endpointData->getTags();
                }

                $pathItem->{$method} = $operation;
            }

            $root->paths->addPath($endpointData->getPath(), $pathItem);
        }

        $root->tags = array_values($tags);

        return $root;
    }

    private function convertRequestModelToRequestBody(RestApiBundle\DTO\OpenApi\Schema\ObjectType $objectType): OpenApi\RequestBody
    {
        return new OpenApi\RequestBody([
            'description' => 'Request body',
            'required' => $objectType->getNullable() === false,
            'content' => [
                'application/json' => [
                    'schema' => $this->convertInnerTypeToOpenApiSchema($objectType),
                ]
            ]
        ]);
    }

    /**
     * @param RestApiBundle\DTO\OpenApi\Schema\ObjectType $objectType
     *
     * @return OpenApi\Parameter[]
     */
    private function convertRequestModelToParameters(RestApiBundle\DTO\OpenApi\Schema\ObjectType $objectType): array
    {
        $result = [];

        foreach ($objectType->getProperties() as $name => $property) {
            $result[] = $this->createParameter('query', $name, $property);
        }

        return $result;
    }

    private function createParameter(string $type, string $name, RestApiBundle\DTO\OpenApi\Schema\TypeInterface $schema): OpenApi\Parameter
    {
        $data = [
            'in' => $type,
            'name' => $name,
            'required' => !$schema->getNullable(),
        ];

        // Swagger UI does not show schema description in parameters
        if ($schema instanceof RestApiBundle\DTO\OpenApi\Schema\DescriptionAwareInterface && $schema->getDescription()) {
            $data['description'] = $schema->getDescription();
            $data['schema'] = $this->convertInnerTypeToOpenApiSchema($schema);
        } else {
            $data['schema'] = $this->convertInnerTypeToOpenApiSchema($schema);
        }

        return new OpenApi\Parameter($data);
    }

    private function convertInnerTypeToOpenApiSchema(RestApiBundle\DTO\OpenApi\Schema\TypeInterface $innerType): OpenApi\Schema
    {
        if ($innerType instanceof RestApiBundle\DTO\OpenApi\Schema\StringType) {
            $result = $this->convertStringType($innerType);
        } elseif ($innerType instanceof RestApiBundle\DTO\OpenApi\Schema\IntegerType) {
            $result = $this->convertIntegerType($innerType);
        } elseif ($innerType instanceof RestApiBundle\DTO\OpenApi\Schema\FloatType) {
            $result = $this->convertFloatType($innerType);
        } elseif ($innerType instanceof RestApiBundle\DTO\OpenApi\Schema\BooleanType) {
            $result = $this->convertBooleanType($innerType);
        } elseif ($innerType instanceof RestApiBundle\DTO\OpenApi\Schema\ArrayType) {
            $result = $this->convertArrayType($innerType);
        } else {
            throw new \InvalidArgumentException();
        }

        return $result;
    }

    private function convertArrayType(RestApiBundle\DTO\OpenApi\Schema\ArrayType $arrayType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::ARRAY,
            'nullable' => $arrayType->getNullable(),
            'items' => $this->convertInnerTypeToOpenApiSchema($arrayType->getInnerType()),
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
}
