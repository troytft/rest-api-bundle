<?php

namespace RestApiBundle\Services\OpenApi;

use RestApiBundle;
use cebe\openapi\spec as OpenApi;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation;
use Symfony\Component\PropertyInfo;

use function array_merge;
use function array_values;
use function ksort;
use function sprintf;
use function strtolower;

class SpecificationGenerator extends RestApiBundle\Services\OpenApi\AbstractSchemaResolver
{
    public function __construct(
        private RestApiBundle\Services\OpenApi\RequestModelResolver $requestModelResolver,
        private RestApiBundle\Services\OpenApi\ResponseModelResolver $responseModelResolver
    ) {
    }

    /**
     * @param RestApiBundle\Model\OpenApi\EndpointData[] $endpoints
     */
    public function generate(array $endpoints, ?OpenApi\OpenApi $template = null): OpenApi\OpenApi
    {
        $root = $template ?: new OpenApi\OpenApi([
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Open API Specification',
                'version' => '1.0.0',
            ],
            'paths' => [],
            'tags' => [],
            'components' => [],
        ]);

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

        foreach ($endpoints as $endpointData) {
            if (!$endpointData->actionRouteMapping->getMethods()) {
                throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Route has empty methods', $endpointData->reflectionMethod);
            }

            if (array_diff($endpointData->actionRouteMapping->getMethods(), self::getSupportedHttpMethods())) {
                throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Route has invalid methods', $endpointData->reflectionMethod);
            }

            $routePath = $this->resolveRoutePath($endpointData);
            $pathItem = $paths[$routePath] ?? null;
            if (!$pathItem) {
                $pathItem = new OpenApi\PathItem([]);
                $paths[$routePath] = $pathItem;
            }

            foreach ($endpointData->actionRouteMapping->getMethods() as $key) {
                $operation = $this->createOperation($endpointData, $key, $routePath);

                foreach ($operation->tags as $tagName) {
                    if (isset($tags[$tagName])) {
                        continue;
                    }

                    $tags[$tagName] = new OpenApi\Tag([
                        'name' => $tagName,
                    ]);
                }

                $key = strtolower($key);
                if (isset($pathItem->getOperations()[$key])) {
                    throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Operation with same url and http method already defined in specification', $endpointData->reflectionMethod);
                }

                $pathItem->{$key} = $operation;
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

    private function resolveRoutePath(RestApiBundle\Model\OpenApi\EndpointData $endpointData): string
    {
        if ($endpointData->controllerRouteMapping instanceof Route && $endpointData->controllerRouteMapping->getPath()) {
            $result = $endpointData->controllerRouteMapping->getPath();
            if ($endpointData->actionRouteMapping->getPath()) {
                $result .= $endpointData->actionRouteMapping->getPath();
            }
        } elseif ($endpointData->actionRouteMapping->getPath()) {
            $result = $endpointData->actionRouteMapping->getPath();
        } else {
            throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Route has empty path', $endpointData->reflectionMethod);
        }

        return $result;
    }

    private function createOperation(RestApiBundle\Model\OpenApi\EndpointData $endpointData, string $httpMethod, string $routePath): OpenApi\Operation
    {
        if (!$endpointData->endpointMapping->title) {
            throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Endpoint has empty title', $endpointData->reflectionMethod);
        }

        if (is_string($endpointData->endpointMapping->tags)) {
            $tags = [$endpointData->endpointMapping->tags];
        } elseif (is_array($endpointData->endpointMapping->tags)) {
            $tags = $endpointData->endpointMapping->tags;
        } else {
            throw new \InvalidArgumentException();
        }

        if (!$tags) {
            throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Endpoint has empty tags', $endpointData->reflectionMethod);
        }

        $parameters = $this->resolveParameters($routePath, $endpointData->reflectionMethod);
        $operation = new OpenApi\Operation([
            'summary' => $endpointData->endpointMapping->title,
            'responses' => $this->resolveResponses($endpointData->reflectionMethod),
            'tags' => $tags,
        ]);

        if ($endpointData->endpointMapping->description) {
            $operation->description = $endpointData->endpointMapping->description;
        }

        $requestModel = $this->findMapperModel($endpointData->reflectionMethod);
        if ($requestModel && $httpMethod === HttpFoundation\Request::METHOD_GET) {
            $parameters = array_merge($parameters, $this->convertRequestModelToParameters($requestModel));
        }

        if ($requestModel && $httpMethod !== HttpFoundation\Request::METHOD_GET) {
            $operation->requestBody = $this->convertRequestModelToRequestBody($requestModel);
        }

        if ($parameters) {
            $operation->parameters = $parameters;
        }

        return $operation;
    }

    private function findMapperModel(\ReflectionMethod $reflectionMethod): ?PropertyInfo\Type
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
     * @return OpenApi\Parameter[]
     */
    private function resolveParameters(string $path, \ReflectionMethod $reflectionMethod): array
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
        $placeholders = $this->extractPathPlaceholders($path);

        foreach ($placeholders as $placeholder) {
            if (isset($scalarTypes[$placeholder])) {
                $result[] = new OpenApi\Parameter([
                    'in' => 'path',
                    'name' => $placeholder,
                    'required' => true,
                    'schema' => $this->resolveScalarType($scalarTypes[$placeholder]),
                ]);
            } elseif (isset($entityTypes[$placeholder])) {
                $result[] = new OpenApi\Parameter([
                    'in' => 'path',
                    'name' => $placeholder,
                    'required' => true,
                    'schema' => $this->resolveSchemaForEntityPathParameter($entityTypes[$placeholder], 'id'),
                ]);
                unset($entityTypes[$placeholder]);
            } else {
                $entityType = reset($entityTypes);
                if (!$entityType instanceof PropertyInfo\Type) {
                    throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException(sprintf('Associated parameter for placeholder %s not matched', $placeholder), $reflectionMethod);
                }
                $result[] = new OpenApi\Parameter([
                    'in' => 'path',
                    'name' => $placeholder,
                    'required' => true,
                    'schema' => $this->resolveSchemaForEntityPathParameter($entityType, $placeholder),
                ]);
            }
        }

        return $result;
    }

    private function resolveSchemaForEntityPathParameter(PropertyInfo\Type $type, string $fieldName): OpenApi\Schema
    {
        $columnType = RestApiBundle\Helper\DoctrineHelper::extractColumnType($type->getClassName(), $fieldName);
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

        $schema->description = sprintf('Element by "%s"', $fieldName);
        $schema->nullable = $type->isNullable();

        return $schema;
    }

    /**
     * @return string[]
     */
    private function extractPathPlaceholders(string $path): array
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

    private function createEmptyResponse(): OpenApi\Response
    {
        return new OpenApi\Response(['description' => 'Success response with empty body']);
    }

    private function resolveResponses(\ReflectionMethod $reflectionMethod): OpenApi\Responses
    {
        $responses = new OpenApi\Responses([]);

        $returnType = RestApiBundle\Helper\TypeExtractor::extractReturnType($reflectionMethod);
        if (!$returnType) {
            throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Return type not found in docBlock and type-hint', $reflectionMethod);
        }

        switch (true) {
            case $returnType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_NULL:
                $responses->addResponse('204', $this->createEmptyResponse());

                break;

            case $returnType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && RestApiBundle\Helper\ClassInstanceHelper::isResponseModel($returnType->getClassName()):
                if ($returnType->isNullable()) {
                    $responses->addResponse('204', $this->createEmptyResponse());
                }

                $responseModelSchema = $this->responseModelResolver->resolveReferenceByClass($returnType->getClassName());
                $responseModelSchema
                    ->nullable = $returnType->isNullable();

                $responses->addResponse('200', new OpenApi\Response([
                    'description' => 'Success response with json body',
                    'content' => [
                        'application/json' => [
                            'schema' => $responseModelSchema,
                        ]
                    ]
                ]));

                break;

            case $returnType->isCollection() && $returnType->getCollectionValueTypes() && RestApiBundle\Helper\TypeExtractor::extractCollectionValueType($returnType)->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT:
                $collectionValueType = RestApiBundle\Helper\TypeExtractor::extractCollectionValueType($returnType);
                if (!RestApiBundle\Helper\ClassInstanceHelper::isResponseModel($collectionValueType->getClassName())) {
                    throw new \InvalidArgumentException('Invalid response type');
                }

                if ($returnType->isNullable()) {
                    $responses->addResponse('204', $this->createEmptyResponse());
                }

                $responses->addResponse('200', new OpenApi\Response([
                    'description' => 'Success response with json body',
                    'content' => [
                        'application/json' => [
                            'schema' => new OpenApi\Schema([
                                'type' => OpenApi\Type::ARRAY,
                                'items' => $this->responseModelResolver->resolveReferenceByClass($collectionValueType->getClassName()),
                                'nullable' => $returnType->isNullable(),
                            ])
                        ]
                    ]
                ]));

                break;

            case $returnType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && $returnType->getClassName() === HttpFoundation\RedirectResponse::class:
                $responses->addResponse('302', new OpenApi\Response([
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

                break;

            case $returnType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && $returnType->getClassName() === HttpFoundation\BinaryFileResponse::class:
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

                break;

            default:
                throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Invalid response type', $reflectionMethod);
        }

        return $responses;
    }

    private static function getSupportedHttpMethods(): array
    {
        return [
            HttpFoundation\Request::METHOD_GET,
            HttpFoundation\Request::METHOD_PUT,
            HttpFoundation\Request::METHOD_POST,
            HttpFoundation\Request::METHOD_DELETE,
            HttpFoundation\Request::METHOD_PATCH,
        ];
    }
}
