<?php

namespace RestApiBundle\Services\OpenApi;

use RestApiBundle;
use cebe\openapi\spec as OpenApi;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\PropertyInfo;

use function array_merge;
use function array_values;
use function file_exists;
use function file_get_contents;
use function in_array;
use function json_decode;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;
use function ksort;
use function pathinfo;
use function sprintf;
use function strtolower;
use function var_dump;

class SpecificationGenerator extends RestApiBundle\Services\OpenApi\AbstractSchemaResolver
{
    private RestApiBundle\Services\OpenApi\RequestModelResolver $requestModelResolver;
    private RestApiBundle\Services\OpenApi\ResponseModelResolver $responseModelResolver;

    public function __construct(
        RestApiBundle\Services\OpenApi\RequestModelResolver $requestModelResolver,
        RestApiBundle\Services\OpenApi\ResponseModelResolver $responseModelResolver
    ) {
        $this->requestModelResolver = $requestModelResolver;
        $this->responseModelResolver = $responseModelResolver;
    }

    /**
     * @param RestApiBundle\Model\OpenApi\EndpointData[] $endpoints
     */
    public function generateYaml(array $endpoints, ?string $template = null): string
    {
        $data = $this
            ->generateSpecification($endpoints, $template)
            ->getSerializableData();

        return Yaml::dump($data, 256, 4, Yaml::DUMP_OBJECT_AS_MAP);
    }

    /**
     * @param RestApiBundle\Model\OpenApi\EndpointData[] $endpoints
     */
    public function generateJson(array $endpoints, ?string $template = null): string
    {
        $data = $this
            ->generateSpecification($endpoints, $template)
            ->getSerializableData();

        $result = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException(json_last_error_msg());
        }

        return $result;
    }

    private function createRootElement(?string $template = null): OpenApi\OpenApi
    {
        $defaultData = [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Open API Specification',
                'version' => '1.0.0',
            ],
            'paths' => [],
            'tags' => [],
            'components' => [],
        ];

        if ($template) {
            if (!file_exists($template)) {
                throw new \InvalidArgumentException(sprintf('File %s does not exist', $template));
            }

            $extension = pathinfo($template, \PATHINFO_EXTENSION) ?? null;
            if (in_array($extension, ['yaml', 'yml'], true)) {
                $defaultData = array_merge($defaultData, Yaml::parseFile($template));
            } elseif ($extension === 'json') {
                $defaultData = array_merge($defaultData, json_decode(file_get_contents($template), true));
            } else {
                throw new \InvalidArgumentException(sprintf('Invalid template file extension'));
            }
        }

        return new OpenApi\OpenApi($defaultData);
    }

    /**
     * @param RestApiBundle\Model\OpenApi\EndpointData[] $endpointDataItems
     */
    private function generateSpecification(array $endpointDataItems, ?string $template = null): OpenApi\OpenApi
    {
        $root = $this->createRootElement($template);

        $paths = [];
        foreach ($root->paths as $path => $pathItem) {
            $paths[$path] = $pathItem;
        }

        $tags = [];
        foreach ($root->tags as $tag) {
            if (!$tag instanceof OpenApi\Tag) {
                throw new \InvalidArgumentException();
            }

            if (!isset($tags[$tag->name])) {
                $tags[$tag->name] = $tag;
            }
        }

        $schemas = [];
        foreach ($root->components->schemas as $typename => $schema) {
            if (!$schema instanceof OpenApi\Schema) {
                throw new \InvalidArgumentException();
            }

            $schemas[$typename] = $schema;
        }

        foreach ($endpointDataItems as $routeData) {
            foreach ($routeData->getTags() as $tagName) {
                if (isset($tags[$tagName])) {
                    continue;
                }

                $tags[$tagName] = new OpenApi\Tag([
                    'name' => $tagName,
                ]);
            }

            $response = $routeData->getResponse();

            $responses = new OpenApi\Responses([]);

            if ($response->getNullable()) {
                $responses->addResponse('204', new OpenApi\Response(['description' => 'Success response with empty body']));
            }

            if ($response instanceof RestApiBundle\Model\OpenApi\Response\ResponseModel) {
                $responseModelSchema = $this->responseModelResolver->resolveReferenceByClass($response->getClass());
                $responseModelSchema
                    ->nullable = $response->getNullable();

                $responses->addResponse('200', new OpenApi\Response([
                    'description' => 'Success response with body',
                    'content' => [
                        'application/json' => [
                            'schema' => $responseModelSchema
                        ]
                    ]
                ]));
            } elseif ($response instanceof RestApiBundle\Model\OpenApi\Response\ArrayOfResponseModels) {
                $responseModelSchema = $this->responseModelResolver->resolveReferenceByClass($response->getClass());
                $responseModelSchema
                    ->nullable = $response->getNullable();

                $responses->addResponse('200', new OpenApi\Response([
                    'description' => 'Success response with body',
                    'content' => [
                        'application/json' => [
                            'schema' => new OpenApi\Schema([
                                'type' => OpenApi\Type::ARRAY,
                                'items' => $responseModelSchema,
                                'nullable' => $response->getNullable(),
                            ])
                        ]
                    ]
                ]));
            }

            $pathParameters = [];

            foreach ($routeData->getPathParameters() as $pathParameter) {
                if ($pathParameter instanceof RestApiBundle\Model\OpenApi\PathParameter\ScalarParameter) {
                    $schema = $this->resolveScalarType(($pathParameter->getType()));
                } elseif ($pathParameter instanceof RestApiBundle\Model\OpenApi\PathParameter\EntityTypeParameter) {
                    $columnType = RestApiBundle\Helper\DoctrineHelper::extractColumnType($pathParameter->getClassType()->getClassName(), $pathParameter->getFieldName());
                    if ($columnType === PropertyInfo\Type::BUILTIN_TYPE_STRING) {
                        $schema = new OpenApi\Schema([
                            'type' => OpenApi\Type::STRING,
                        ]);
                    } elseif ($columnType === PropertyInfo\Type::BUILTIN_TYPE_INT) {
                        $schema = new OpenApi\Schema([
                            'type' => OpenApi\Type::INTEGER,
                        ]);
                    } else {
                        var_dump($pathParameter->getClassType()->getClassName(), $pathParameter->getFieldName());
                        throw new \InvalidArgumentException();
                    }


                    $schema->description = sprintf('Element by "%s"', $pathParameter->getFieldName());
                    $schema->nullable = $pathParameter->getClassType()->isNullable();

                } else {
                    throw new \InvalidArgumentException();
                }

                $pathParameters[] = new OpenApi\Parameter([
                    'in' => 'path',
                    'name' => $pathParameter->getName(),
                    'required' => true,
                    'schema' => $schema,
                ]);
            }

            $pathItem = $paths[$routeData->getPath()] ?? null;
            if (!$pathItem) {
                $pathItem = new OpenApi\PathItem([]);
                $paths[$routeData->getPath()] = $pathItem;
            }

            foreach ($routeData->getMethods() as $httpMethod) {
                $httpMethod = strtolower($httpMethod);
                $isHttpGetMethod = $httpMethod === 'get';

                if (isset($pathItem->getOperations()[$httpMethod])) {
                    throw new \InvalidArgumentException(sprintf('Route already defined %s %s', $httpMethod, $routeData->getPath()));
                }

                $operation = new OpenApi\Operation([
                    'summary' => $routeData->getTitle(),
                    'responses' => $responses,
                ]);

                if ($routeData->getDescription()) {
                    $operation->description = $routeData->getDescription();
                }

                $queryParameters = [];
                $request = $routeData->getRequest();
                if ($request instanceof RestApiBundle\Model\OpenApi\Request\RequestModel && $isHttpGetMethod) {
                    $queryParameters = $this->convertRequestModelToParameters($request);
                }

                if ($request instanceof RestApiBundle\Model\OpenApi\Request\RequestModel && !$isHttpGetMethod) {
                    $operation->requestBody = $this->convertRequestModelToRequestBody($request);
                }

                if ($pathParameters || $queryParameters) {
                    $operation->parameters = array_merge($pathParameters, $queryParameters);
                }

                if ($routeData->getTags()) {
                    $operation->tags = $routeData->getTags();
                }

                $pathItem->{$httpMethod} = $operation;
            }
        }

        ksort($paths);
        $root->paths = new OpenApi\Paths($paths);

        ksort($tags);
        $root->tags = array_values($tags);

        foreach ($this->responseModelResolver->dumpSchemas() as $typename => $schema) {
            if (isset($schemas[$typename])) {
                throw new \InvalidArgumentException(sprintf('Schema with typename %s already defined', $typename));
            }

            $schemas[$typename] = $schema;
        }

        ksort($schemas);
        $root->components->schemas = $schemas;

        return $root;
    }

    private function convertRequestModelToRequestBody(RestApiBundle\Model\OpenApi\Request\RequestModel $requestModel): OpenApi\RequestBody
    {
        $schema = $this->requestModelResolver->resolveByClass($requestModel->getClass());
        $schema
            ->nullable = $requestModel->getNullable();

        return new OpenApi\RequestBody([
            'description' => 'Request body',
            'required' => !$requestModel->getNullable(),
            'content' => [
                'application/json' => [
                    'schema' => $schema,
                ]
            ]
        ]);
    }

    /**
     * @return OpenApi\Parameter[]
     */
    private function convertRequestModelToParameters(RestApiBundle\Model\OpenApi\Request\RequestModel $requestModel): array
    {
        $result = [];
        $schema = $this->requestModelResolver->resolveByClass($requestModel->getClass());

        foreach ($schema->properties as $propertyName => $propertySchema) {
            $parameter = new OpenApi\Parameter([
                'in' => 'query',
                'name' => $propertyName,
                'required' => !$propertySchema->nullable,
                'schema' => $propertySchema,
            ]);

            // Swagger UI does not show schema description in parameters
            if ($propertySchema->description) {
                $parameter->description = $propertySchema->description;
                unset($propertySchema->description);
            }

            $result[] = $parameter;
        }

        return $result;
    }
}
