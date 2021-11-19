<?php

namespace RestApiBundle\Services\OpenApi;

use RestApiBundle;
use cebe\openapi\spec as OpenApi;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation;
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

            $extension = pathinfo($template, \PATHINFO_EXTENSION);
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
            if (is_string($routeData->endpointMapping->tags)) {
                $endpointTags = [$routeData->endpointMapping->tags];
            } elseif (is_array($routeData->endpointMapping->tags)) {
                $endpointTags = $routeData->endpointMapping->tags;
            } else {
                throw new \InvalidArgumentException();
            }

            if (!$routeData->endpointMapping->title) {
                throw RestApiBundle\Exception\ContextAware\FunctionOfClassException::fromMessageAndReflectionMethod('Endpoint has empty title', $routeData->reflectionMethod);
            }

            if ($routeData->controllerRouteMapping instanceof Route && $routeData->controllerRouteMapping->getPath()) {
                $routePath = $routeData->controllerRouteMapping->getPath();

                if ($routeData->actionRouteMapping->getPath()) {
                    $routePath .= $routeData->actionRouteMapping->getPath();
                }
            } elseif ($routeData->actionRouteMapping->getPath()) {
                $routePath = $routeData->actionRouteMapping->getPath();
            } else {
                throw RestApiBundle\Exception\ContextAware\FunctionOfClassException::fromMessageAndReflectionMethod('Route has empty path', $routeData->reflectionMethod);
            }

            if (!$routeData->actionRouteMapping->getMethods()) {
                throw RestApiBundle\Exception\ContextAware\FunctionOfClassException::fromMessageAndReflectionMethod('Route has empty methods', $routeData->reflectionMethod);
            }

            if (!$endpointTags) {
                throw RestApiBundle\Exception\ContextAware\FunctionOfClassException::fromMessageAndReflectionMethod('Endpoint has empty tags', $routeData->reflectionMethod);
            }

            foreach ($endpointTags as $tagName) {
                if (isset($tags[$tagName])) {
                    continue;
                }

                $tags[$tagName] = new OpenApi\Tag([
                    'name' => $tagName,
                ]);
            }

            $response = $this->extractResponse($routeData->reflectionMethod);

            $responses = new OpenApi\Responses([]);

            if ($response->getNullable()) {
                $responses->addResponse('204', new OpenApi\Response(['description' => 'Success response with empty body']));
            }

            if ($response instanceof RestApiBundle\Model\OpenApi\Response\ResponseModel) {
                $responseModelSchema = $this->responseModelResolver->resolveReferenceByClass($response->getClass());
                $responseModelSchema
                    ->nullable = $response->getNullable();

                $responses->addResponse('200', new OpenApi\Response([
                    'description' => 'Success response with json body',
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
                    'description' => 'Success response with json body',
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
            } elseif ($response instanceof RestApiBundle\Model\OpenApi\Response\RedirectResponse) {
                $responses->addResponse((string) $response->getStatusCode(), new OpenApi\Response([
                    'description' => 'Success response with redirect',
                    'headers' => [
                        'Location' => [
                            'schema' => new OpenApi\Schema([
                                'type' => OpenApi\Type::STRING,
                                'example' => 'https://example.com'
                            ]),
                            'description' => 'Redirect URL',
                        ]
                    ]
                ]));
            } elseif ($response instanceof RestApiBundle\Model\OpenApi\Response\BinaryFileResponse) {
                $responses->addResponse('200', new OpenApi\Response([
                    'description' => 'Success binary file response',
                    'headers' => [
                        'Content-Type' => [
                            'schema' => new OpenApi\Schema([
                                'type' => OpenApi\Type::STRING,
                                'example' => 'application/octet-stream'
                            ]),
                            'description' => 'File mime type',
                        ]
                    ]
                ]));
            }

            $pathParameters = [];

            foreach ($this->extractPathParameters($routePath, $routeData->reflectionMethod) as $pathParameter) {
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

            $pathItem = $paths[$routePath] ?? null;
            if (!$pathItem) {
                $pathItem = new OpenApi\PathItem([]);
                $paths[$routePath] = $pathItem;
            }

            foreach ($routeData->actionRouteMapping->getMethods() as $httpMethod) {
                $httpMethod = strtolower($httpMethod);
                $isHttpGetMethod = $httpMethod === 'get';

                if (isset($pathItem->getOperations()[$httpMethod])) {
                    throw new \InvalidArgumentException(sprintf('Route already defined %s %s', $httpMethod, $routePath));
                }

                $operation = new OpenApi\Operation([
                    'summary' => $routeData->endpointMapping->title,
                    'responses' => $responses,
                    'tags' => $endpointTags,
                ]);

                if ($routeData->endpointMapping->description) {
                    $operation->description = $routeData->endpointMapping->description;
                }

                $queryParameters = [];
                $request = $this->extractRequest($routeData->reflectionMethod);
                if ($request && $isHttpGetMethod) {
                    $queryParameters = $this->convertRequestModelToParameters($request);
                }

                if ($request && !$isHttpGetMethod) {
                    $operation->requestBody = $this->convertRequestModelToRequestBody($request);
                }

                if ($pathParameters || $queryParameters) {
                    $operation->parameters = array_merge($pathParameters, $queryParameters);
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

    private function extractResponse(\ReflectionMethod $reflectionMethod): RestApiBundle\Model\OpenApi\Response\ResponseInterface
    {
        $returnType = RestApiBundle\Helper\TypeExtractor::extractReturnType($reflectionMethod);
        if (!$returnType) {
            throw RestApiBundle\Exception\ContextAware\FunctionOfClassException::fromMessageAndReflectionMethod('Return type not found in docBlock and type-hint', $reflectionMethod);
        }

        switch (true) {
            case $returnType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_NULL:
                $result = new RestApiBundle\Model\OpenApi\Response\EmptyResponse();

                break;

            case $returnType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && RestApiBundle\Helper\ClassInstanceHelper::isResponseModel($returnType->getClassName()):
                $result = new RestApiBundle\Model\OpenApi\Response\ResponseModel($returnType->getClassName(), $returnType->isNullable());

                break;

            case $returnType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && $returnType->getClassName() === HttpFoundation\RedirectResponse::class:
                $result = new RestApiBundle\Model\OpenApi\Response\RedirectResponse();

                break;

            case $returnType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && $returnType->getClassName() === HttpFoundation\BinaryFileResponse::class:
                $result = new RestApiBundle\Model\OpenApi\Response\BinaryFileResponse();

                break;

            case $returnType->isCollection() && $returnType->getCollectionValueTypes() && RestApiBundle\Helper\TypeExtractor::extractCollectionValueType($returnType)->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT:
                $collectionValueType = RestApiBundle\Helper\TypeExtractor::extractCollectionValueType($returnType);
                if (!RestApiBundle\Helper\ClassInstanceHelper::isResponseModel($collectionValueType->getClassName())) {
                    throw new \InvalidArgumentException('Invalid response type');
                }

                $result = new RestApiBundle\Model\OpenApi\Response\ArrayOfResponseModels($collectionValueType->getClassName(), $returnType->isNullable());

                break;

            default:
                throw new \InvalidArgumentException('Invalid response type');
        }

        return $result;
    }

    private function extractRequest(\ReflectionMethod $reflectionMethod): ?PropertyInfo\Type
    {
        $result = null;

        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            if (!$reflectionParameter->getType()) {
                continue;
            }

            $parameterType = RestApiBundle\Helper\TypeExtractor::extractByReflectionType($reflectionParameter->getType());
            if ($parameterType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && RestApiBundle\Helper\ClassInstanceHelper::isMapperModel($parameterType->getClassName())) {
                $result = $parameterType;

                break;
            }
        }

        return $result;
    }

    /**
     * @return RestApiBundle\Model\OpenApi\PathParameter\PathParameterInterface[]
     */
    private function extractPathParameters(string $path, \ReflectionMethod $reflectionMethod): array
    {
        $scalarTypes = [];
        $entityTypes = [];

        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            if (!$reflectionParameter->getType()) {
                continue;
            }

            $parameterType = RestApiBundle\Helper\TypeExtractor::extractByReflectionType($reflectionParameter->getType());
            if (RestApiBundle\Helper\TypeExtractor::isScalar($parameterType)) {
                $scalarTypes[$reflectionParameter->getName()] = $parameterType;
            } elseif ($parameterType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && RestApiBundle\Helper\DoctrineHelper::isEntity($parameterType->getClassName())) {
                $entityTypes[$reflectionParameter->getName()] = $parameterType;
            }
        }

        $result = [];
        $placeholders = $this->getPathPlaceholders($path);

        foreach ($placeholders as $placeholder) {
            if (isset($scalarTypes[$placeholder])) {
                $result[] = new RestApiBundle\Model\OpenApi\PathParameter\ScalarParameter($placeholder, $scalarTypes[$placeholder]);
            } elseif (isset($entityTypes[$placeholder])) {
                $result[] = new RestApiBundle\Model\OpenApi\PathParameter\EntityTypeParameter($placeholder, $entityTypes[$placeholder], 'id');
                unset($entityTypes[$placeholder]);
            } else {
                $entityType = reset($entityTypes);
                if (!$entityType instanceof PropertyInfo\Type) {
                    throw RestApiBundle\Exception\ContextAware\FunctionOfClassException::fromMessageAndReflectionMethod(sprintf('Associated parameter for placeholder %s not matched', $placeholder), $reflectionMethod);
                }
                $result[] = new RestApiBundle\Model\OpenApi\PathParameter\EntityTypeParameter($placeholder, $entityType, $placeholder);
            }
        }

        return $result;
    }

    private function getPathPlaceholders(string $path): array
    {
        $matches = null;
        $parameters = [];

        if (preg_match_all('/{([^}]+)}/', $path, $matches)) {
            $parameters = $matches[1];
        }

        return $parameters;
    }

    private function convertRequestModelToRequestBody(PropertyInfo\Type $requestModel): OpenApi\RequestBody
    {
        $schema = $this->requestModelResolver->resolveByClass($requestModel->getClassName());
        $schema
            ->nullable = $requestModel->isNullable();

        return new OpenApi\RequestBody([
            'description' => 'Request body',
            'required' => !$requestModel->isNullable(),
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
    private function convertRequestModelToParameters(PropertyInfo\Type $requestModel): array
    {
        $result = [];
        $schema = $this->requestModelResolver->resolveByClass($requestModel->getClassName());

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
