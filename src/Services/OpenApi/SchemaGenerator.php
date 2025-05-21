<?php

declare(strict_types=1);

namespace RestApiBundle\Services\OpenApi;

use cebe\openapi\spec as OpenApi;
use RestApiBundle;
use Symfony\Component\HttpFoundation;
use Symfony\Component\PropertyInfo;
use Symfony\Component\Routing\Annotation\Route;

class SchemaGenerator
{
    public function __construct(
        private RequestModelResolver $requestModelResolver,
        private ResponseModelResolver $responseModelResolver,
        private RestApiBundle\Services\PropertyInfoExtractorService $propertyInfoExtractorService,
    ) {
    }

    /**
     * @param RestApiBundle\Model\OpenApi\EndpointData[] $endpoints
     */
    public function generate(array $endpoints, ?OpenApi\OpenApi $template = null): OpenApi\OpenApi
    {
        $paths = [];
        $tags = [];
        $schemas = [];

        if ($template) {
            $rootElement = $template;

            foreach ($rootElement->paths as $path => $pathItem) {
                $paths[$path] = $pathItem;
            }
            foreach ($rootElement->tags as $tag) {
                if (!isset($tags[$tag->name])) {
                    $tags[$tag->name] = $tag;
                }
            }
            foreach ($rootElement->components->schemas as $typename => $schema) {
                $schemas[$typename] = $schema;
            }
        } else {
            $rootElement = new OpenApi\OpenApi([
                'openapi' => '3.1.0',
                'info' => [
                    'title' => 'Open API Specification',
                    'version' => '1.0.0',
                ],
                'paths' => [],
                'tags' => [],
                'components' => [],
            ]);
        }

        foreach ($endpoints as $endpointData) {
            $endpointPath = $this->resolveEndpointPath($endpointData);
            if (!isset($paths[$endpointPath])) {
                $paths[$endpointPath] = new OpenApi\PathItem([]);
            }

            $pathItem = $paths[$endpointPath];

            foreach ($endpointData->actionRouteMapping->getMethods() as $method) {
                $operation = $this->createOperation($endpointData, $method, $endpointPath);

                foreach ($operation->tags as $tagName) {
                    if (!isset($tags[$tagName])) {
                        $tags[$tagName] = new OpenApi\Tag([
                            'name' => $tagName,
                        ]);
                    }
                }

                $method = strtolower($method);
                if (isset($pathItem->getOperations()[$method])) {
                    throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Operation with same url and method already defined in specification', $endpointData->reflectionMethod);
                }

                $pathItem->{$method} = $operation;
            }
        }

        ksort($paths);
        $rootElement->paths = new OpenApi\Paths($paths);

        ksort($tags);
        $rootElement->tags = array_values($tags);

        foreach ($this->responseModelResolver->dumpSchemas() as $typename => $schema) {
            if (isset($schemas[$typename])) {
                throw new \InvalidArgumentException(\sprintf('Schema with typename %s already defined', $typename));
            }

            $schemas[$typename] = $schema;
        }

        if ($schemas) {
            ksort($schemas);
            $rootElement->components->schemas = $schemas;
        }

        return $rootElement;
    }

    private function resolveEndpointPath(RestApiBundle\Model\OpenApi\EndpointData $endpointData): string
    {
        if (!$endpointData->actionRouteMapping->getMethods()) {
            throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Route has empty methods', $endpointData->reflectionMethod);
        }

        $allowedMethods = [
            HttpFoundation\Request::METHOD_GET,
            HttpFoundation\Request::METHOD_PUT,
            HttpFoundation\Request::METHOD_POST,
            HttpFoundation\Request::METHOD_DELETE,
            HttpFoundation\Request::METHOD_PATCH,
        ];
        if (array_diff($endpointData->actionRouteMapping->getMethods(), $allowedMethods)) {
            throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Route has invalid methods', $endpointData->reflectionMethod);
        }

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
        $operation = new OpenApi\Operation([
            'summary' => $endpointData->endpointMapping->title,
            'responses' => $this->createResponses($endpointData->reflectionMethod, $endpointData->endpointMapping->httpStatusCode),
            'tags' => match (true) {
                \is_string($endpointData->endpointMapping->tags) => [$endpointData->endpointMapping->tags],
                \is_array($endpointData->endpointMapping->tags) => $endpointData->endpointMapping->tags,
            },
        ]);

        if (!$operation->summary) {
            throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Title can not be empty', $endpointData->reflectionMethod);
        }

        if (!$operation->tags) {
            throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Tags can not be empty', $endpointData->reflectionMethod);
        }

        if ($endpointData->endpointMapping->description) {
            $operation->description = $endpointData->endpointMapping->description;
        }

        if ($endpointData->deprecated) {
            $operation->deprecated = true;
        }

        $scalarTypes = [];
        $doctrineEntityTypes = [];
        $requestModelType = null;

        foreach ($endpointData->reflectionMethod->getParameters() as $reflectionMethodParameter) {
            $reflectionMethodType = RestApiBundle\Helper\TypeExtractor::extractByReflectionParameter($reflectionMethodParameter);
            if (!$reflectionMethodType) {
                continue;
            }

            if (RestApiBundle\Helper\TypeExtractor::isScalar($reflectionMethodType)) {
                $scalarTypes[$reflectionMethodParameter->getName()] = $reflectionMethodType;
            } elseif ($reflectionMethodType->getClassName() && RestApiBundle\Helper\DoctrineHelper::isEntity($reflectionMethodType->getClassName())) {
                $doctrineEntityTypes[$reflectionMethodParameter->getName()] = $reflectionMethodType;
            } elseif ($reflectionMethodType->getClassName() && RestApiBundle\Helper\ReflectionHelper::isMapperModel($reflectionMethodType->getClassName())) {
                if ($requestModelType) {
                    throw new \LogicException();
                }

                $requestModelType = $reflectionMethodType;
            }
        }

        $operationParameters = [];

        foreach ($this->extractPathPlaceholders($routePath) as $parameterName) {
            if (isset($scalarTypes[$parameterName])) {
                $operationParameters[] = $this->createScalarPathParameter($parameterName, $scalarTypes[$parameterName]);
            } elseif (isset($doctrineEntityTypes[$parameterName])) {
                $operationParameters[] = $this->createDoctrineEntityPathParameter($parameterName, $doctrineEntityTypes[$parameterName], 'id');
                unset($doctrineEntityTypes[$parameterName]);
            } else {
                $doctrineEntityType = reset($doctrineEntityTypes);
                if (!$doctrineEntityType instanceof PropertyInfo\Type) {
                    throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException(\sprintf('Associated parameter for placeholder %s not matched', $parameterName), $endpointData->reflectionMethod);
                }

                $operationParameters[] = $this->createDoctrineEntityPathParameter($parameterName, $doctrineEntityType, $parameterName);
            }
        }

        if ($requestModelType && $endpointData->endpointMapping->requestModel) {
            throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('RequestModel already defined by action arguments', $endpointData->reflectionMethod);
        } elseif ($endpointData->endpointMapping->requestModel) {
            $requestModelType = new PropertyInfo\Type(PropertyInfo\Type::BUILTIN_TYPE_OBJECT, false, $endpointData->endpointMapping->requestModel);
        }

        if ($requestModelType && $httpMethod === HttpFoundation\Request::METHOD_GET) {
            $operationParameters = array_merge($operationParameters, $this->createQueryParametersFromRequestModel($requestModelType->getClassName()));
        } elseif ($requestModelType) {
            $operation->requestBody = $this->createRequestBodyFromRequestModel($requestModelType->getClassName());
        }

        if ($operationParameters) {
            $operation->parameters = $operationParameters;
        }

        return $operation;
    }

    private function createScalarPathParameter(string $name, PropertyInfo\Type $type): OpenApi\Parameter
    {
        return new OpenApi\Parameter([
            'in' => 'path',
            'name' => $name,
            'required' => true,
            'schema' => RestApiBundle\Helper\OpenApi\SchemaHelper::createScalarFromPropertyInfoType($type),
        ]);
    }

    private function createDoctrineEntityPathParameter(string $name, PropertyInfo\Type $type, string $entityFieldName): OpenApi\Parameter
    {
        $propertyType = $this->propertyInfoExtractorService->getRequiredPropertyType($type->getClassName(), $entityFieldName);
        if ($propertyType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_INT) {
            $schema = RestApiBundle\Helper\OpenApi\SchemaHelper::createInteger(false);
        } elseif ($propertyType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_STRING) {
            $schema = RestApiBundle\Helper\OpenApi\SchemaHelper::createString(false);
        } else {
            throw new RestApiBundle\Exception\ContextAware\UnknownPropertyTypeException($type->getClassName(), $entityFieldName);
        }

        return new OpenApi\Parameter([
            'in' => 'path',
            'name' => $name,
            'required' => !$type->isNullable(),
            'schema' => $schema,
            'description' => \sprintf('Element by "%s"', $entityFieldName),
        ]);
    }

    /**
     * @return string[]
     */
    private function extractPathPlaceholders(string $path): array
    {
        $matches = null;
        $placeholders = [];

        if (preg_match_all('/{([^}]+)}/', $path, $matches)) {
            $placeholders = $matches[1];
        }

        return $placeholders;
    }

    private function createResponses(\ReflectionMethod $reflectionMethod, ?int $httpStatusCode = null): OpenApi\Responses
    {
        $responses = new OpenApi\Responses([]);

        $returnType = RestApiBundle\Helper\TypeExtractor::extractByReflectionMethod($reflectionMethod);
        if (!$returnType) {
            throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Return type is not specified', $reflectionMethod);
        }

        if ($returnType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_NULL || $returnType->isNullable()) {
            $this->addEmptyResponse($responses, $httpStatusCode);
        }

        if ($returnType->isCollection()) {
            $collectionValueType = RestApiBundle\Helper\TypeExtractor::extractCollectionValueType($returnType);
            if (!$collectionValueType->getClassName() || !RestApiBundle\Helper\ReflectionHelper::isResponseModel($collectionValueType->getClassName())) {
                throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Invalid response type, only collection of response models allowed', $reflectionMethod);
            }

            $this->addCollectionOfResponseModelsResponse($responses, $returnType, $httpStatusCode);
        } elseif ($returnType->getClassName()) {
            if (RestApiBundle\Helper\ReflectionHelper::isResponseModel($returnType->getClassName())) {
                $this->addSingleResponseModelResponse($responses, $returnType, $httpStatusCode);
            } elseif ($returnType->getClassName() === HttpFoundation\RedirectResponse::class) {
                $this->addRedirectResponse($responses, $httpStatusCode);
            } elseif ($returnType->getClassName() === HttpFoundation\BinaryFileResponse::class) {
                $this->addBinaryFileResponse($responses, $httpStatusCode);
            } else {
                throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException(\sprintf('Unknown response class type "%s"', $returnType->getClassName()), $reflectionMethod);
            }
        }

        return $responses;
    }

    private function addEmptyResponse(OpenApi\Responses $responses, ?int $httpStatusCode = null): void
    {
        $httpStatusCode = $httpStatusCode ?? 204;

        $responses->addResponse((string) $httpStatusCode, new OpenApi\Response(['description' => 'Response with empty body']));
    }

    private function addBinaryFileResponse(OpenApi\Responses $responses, ?int $httpStatusCode = null): void
    {
        $httpStatusCode = $httpStatusCode ?? 200;

        $responses->addResponse((string) $httpStatusCode, new OpenApi\Response([
            'description' => 'Response with file download',
            'headers' => [
                'Content-Type' => [
                    'schema' => new OpenApi\Schema([
                        'type' => OpenApi\Type::STRING,
                        'example' => 'application/octet-stream',
                    ]),
                    'description' => 'File mime type',
                ],
            ],
        ]));
    }

    private function addRedirectResponse(OpenApi\Responses $responses, ?int $httpStatusCode = null): void
    {
        $httpStatusCode = $httpStatusCode ?? 302;

        $responses->addResponse((string) $httpStatusCode, new OpenApi\Response([
            'description' => 'Response with redirect',
            'headers' => [
                'Location' => [
                    'schema' => new OpenApi\Schema([
                        'type' => OpenApi\Type::STRING,
                        'example' => 'https://example.com',
                    ]),
                    'description' => 'Redirect URL',
                ],
            ],
        ]));
    }

    private function addSingleResponseModelResponse(OpenApi\Responses $responses, PropertyInfo\Type $returnType, ?int $httpStatusCode = null): void
    {
        $httpStatusCode = $httpStatusCode ?? 200;

        $responses->addResponse((string) $httpStatusCode, new OpenApi\Response([
            'description' => 'Response with JSON body',
            'content' => [
                'application/json' => [
                    'schema' => $this->responseModelResolver->resolveReference($returnType->getClassName()),
                ],
            ],
        ]));
    }

    private function addCollectionOfResponseModelsResponse(OpenApi\Responses $responses, PropertyInfo\Type $returnType, ?int $httpStatusCode = null): void
    {
        $httpStatusCode = $httpStatusCode ?? 200;

        $responses->addResponse((string) $httpStatusCode, new OpenApi\Response([
            'description' => 'Response body',
            'content' => [
                'application/json' => [
                    'schema' => new OpenApi\Schema([
                        'type' => OpenApi\Type::ARRAY,
                        'items' => $this->responseModelResolver->resolveReference($returnType->getCollectionValueTypes()[0]->getClassName()),
                        'nullable' => $returnType->isNullable(),
                    ]),
                ],
            ],
        ]));
    }

    private function createRequestBodyFromRequestModel(string $class): OpenApi\RequestBody
    {
        $schema = $this->requestModelResolver->resolve($class);

        $contentType = 'application/json';
        foreach ($schema->properties as $schemaProperty) {
            if ($schemaProperty->type === OpenApi\Type::STRING && $schemaProperty->format === 'binary') {
                $contentType = 'multipart/form-data';

                break;
            }
        }

        return new OpenApi\RequestBody([
            'description' => 'Request body',
            'required' => true,
            'content' => [
                $contentType => [
                    'schema' => $schema,
                ],
            ],
        ]);
    }

    /**
     * @return OpenApi\Parameter[]
     */
    private function createQueryParametersFromRequestModel(string $class): array
    {
        $queryParameters = [];
        $requestModelSchema = $this->requestModelResolver->resolve($class);

        foreach ($requestModelSchema->properties as $propertyName => $propertySchema) {
            $parameter = new OpenApi\Parameter([
                'in' => 'query',
                'name' => $propertySchema->type === OpenApi\Type::ARRAY ? \sprintf('%s[]', $propertyName) : $propertyName,
                'required' => !$propertySchema->nullable,
                'schema' => $propertySchema,
            ]);

            if ($propertySchema->type === OpenApi\Type::OBJECT) {
                $parameter->style = 'deepObject';
            }

            // Swagger UI shows description only from parameters
            if ($propertySchema->description) {
                $parameter->description = $propertySchema->description;
                unset($propertySchema->description);
            }

            $queryParameters[] = $parameter;
        }

        return $queryParameters;
    }
}
