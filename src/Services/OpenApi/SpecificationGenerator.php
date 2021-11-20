<?php

namespace RestApiBundle\Services\OpenApi;

use RestApiBundle;
use cebe\openapi\spec as OpenApi;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation;
use Symfony\Component\PropertyInfo;

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
        $paths = [];
        $tags = [];
        $schemas = [];

        if ($template) {
            $root = $template;

            foreach ($root->paths as $path => $pathItem) {
                $paths[$path] = $pathItem;
            }
            foreach ($root->tags as $tag) {
                if (!isset($tags[$tag->name])) {
                    $tags[$tag->name] = $tag;
                }
            }
            foreach ($root->components->schemas as $typename => $schema) {
                $schemas[$typename] = $schema;
            }
        } else {
            $root = new OpenApi\OpenApi([
                'openapi' => '3.0.0',
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
                    throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Operation with same url and http method already defined in specification', $endpointData->reflectionMethod);
                }

                $pathItem->{$method} = $operation;
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
            'responses' => $this->createResponses($endpointData->reflectionMethod),
            'tags' => match (true) {
                is_string($endpointData->endpointMapping->tags) => [$endpointData->endpointMapping->tags],
                is_array($endpointData->endpointMapping->tags) => $endpointData->endpointMapping->tags,
                default => throw new \InvalidArgumentException(),
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

        $scalarTypes = [];
        $doctrineEntityTypes = [];
        $requestModelType = null;

        foreach ($endpointData->reflectionMethod->getParameters() as $reflectionMethodParameter) {
            if (!$reflectionMethodParameter->getType()) {
                continue;
            }

            $reflectionMethodType = RestApiBundle\Helper\TypeExtractor::extractByReflectionType($reflectionMethodParameter->getType());
            if (RestApiBundle\Helper\TypeExtractor::isScalar($reflectionMethodType)) {
                $scalarTypes[$reflectionMethodParameter->getName()] = $reflectionMethodType;
            } elseif ($reflectionMethodType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && RestApiBundle\Helper\DoctrineHelper::isEntity($reflectionMethodType->getClassName())) {
                $doctrineEntityTypes[$reflectionMethodParameter->getName()] = $reflectionMethodType;
            } elseif ($reflectionMethodType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && RestApiBundle\Helper\ClassInstanceHelper::isMapperModel($reflectionMethodType->getClassName())) {
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
                    throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException(sprintf('Associated parameter for placeholder %s not matched', $parameterName), $endpointData->reflectionMethod);
                }

                $operationParameters[] = $this->createDoctrineEntityPathParameter($parameterName, $doctrineEntityType, $parameterName);
            }
        }

        if ($requestModelType && $httpMethod === HttpFoundation\Request::METHOD_GET) {
            $operationParameters = array_merge($operationParameters, $this->createQueryParametersFromRequestModel($requestModelType));
        } elseif ($requestModelType) {
            $operation->requestBody = $this->createRequestBody($requestModelType);
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
            'schema' => $this->resolveScalarType($type->getBuiltinType(), $type->isNullable()),
        ]);
    }

    private function createDoctrineEntityPathParameter(string $name, PropertyInfo\Type $type, string $entityFieldName): OpenApi\Parameter
    {
        $entityColumnType = RestApiBundle\Helper\DoctrineHelper::extractColumnType($type->getClassName(), $entityFieldName);

        return new OpenApi\Parameter([
            'in' => 'path',
            'name' => $name,
            'required' => !$type->isNullable(),
            'schema' => $this->resolveScalarType($entityColumnType, $type->isNullable()),
            'description' => sprintf('Element by "%s"', $entityFieldName),
        ]);
    }

    /**
     * @return OpenApi\Parameter[]
     */
    private function createQueryParametersFromRequestModel(PropertyInfo\Type $type): array
    {
        $result = [];
        $requestModelSchema = $this->requestModelResolver->resolveByClass($type->getClassName());

        foreach ($requestModelSchema->properties as $propertyName => $propertySchema) {
            $parameter = new OpenApi\Parameter([
                'in' => 'query',
                'name' => $propertyName,
                'required' => !$propertySchema->nullable,
                'schema' => $propertySchema,
            ]);

            // Swagger UI shows description only from parameters
            if ($propertySchema->description) {
                $parameter->description = $propertySchema->description;
                unset($propertySchema->description);
            }

            $result[] = $parameter;
        }

        return $result;
    }

    private function createRequestBody(PropertyInfo\Type $type): OpenApi\RequestBody
    {
        return new OpenApi\RequestBody([
            'description' => 'Request body',
            'required' => true,
            'content' => [
                'application/json' => [
                    'schema' => $this->requestModelResolver->resolveByClass($type->getClassName()),
                ]
            ]
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

    private function createResponses(\ReflectionMethod $reflectionMethod): OpenApi\Responses
    {
        $responses = new OpenApi\Responses([]);

        $returnType = RestApiBundle\Helper\TypeExtractor::extractReturnType($reflectionMethod);
        if (!$returnType) {
            throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Return type is not specified', $reflectionMethod);
        }

        switch (true) {
            case $returnType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_NULL:
                $responses->addResponse('204', $this->createEmptyBodyResponse());

                break;

            case $returnType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && RestApiBundle\Helper\ClassInstanceHelper::isResponseModel($returnType->getClassName()):
                if ($returnType->isNullable()) {
                    $responses->addResponse('204', $this->createEmptyBodyResponse());
                }

                $responses->addResponse('200', $this->createSingleResponseModelResponse($returnType));

                break;

            case $returnType->isCollection() && $returnType->getCollectionValueTypes() && RestApiBundle\Helper\TypeExtractor::extractCollectionValueType($returnType)->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT:
                $collectionValueType = RestApiBundle\Helper\TypeExtractor::extractCollectionValueType($returnType);
                if (!RestApiBundle\Helper\ClassInstanceHelper::isResponseModel($collectionValueType->getClassName())) {
                    throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Invalid response type, only collection of response models allowed', $reflectionMethod);
                }

                if ($returnType->isNullable()) {
                    $responses->addResponse('204', $this->createEmptyBodyResponse());
                }

                $responses->addResponse('200', $this->createResponseModelCollectionResponse($returnType));

                break;

            case $returnType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && $returnType->getClassName() === HttpFoundation\RedirectResponse::class:
                $responses->addResponse('302', $this->createRedirectResponse());

                break;

            case $returnType->getBuiltinType() === PropertyInfo\Type::BUILTIN_TYPE_OBJECT && $returnType->getClassName() === HttpFoundation\BinaryFileResponse::class:
                $responses->addResponse('200', $this->createBinaryFileResponse());

                break;

            default:
                throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Unknown response type', $reflectionMethod);
        }

        return $responses;
    }

    private function createEmptyBodyResponse(): OpenApi\Response
    {
        return new OpenApi\Response(['description' => 'Success response with empty body']);
    }

    private function createBinaryFileResponse(): OpenApi\Response
    {
        return new OpenApi\Response([
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
        ]);
    }

    private function createRedirectResponse(): OpenApi\Response
    {
        return new OpenApi\Response([
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
        ]);
    }

    private function createSingleResponseModelResponse(PropertyInfo\Type $type): OpenApi\Response
    {
        $schema = $this->responseModelResolver->resolveReferenceByClass($type->getClassName());
        $schema
            ->nullable = $type->isNullable();

        return new OpenApi\Response([
            'description' => 'Success response with json body',
            'content' => [
                'application/json' => [
                    'schema' => $schema,
                ]
            ]
        ]);
    }

    private function createResponseModelCollectionResponse(PropertyInfo\Type $type): OpenApi\Response
    {
        return new OpenApi\Response([
            'description' => 'Success response with json body',
            'content' => [
                'application/json' => [
                    'schema' => new OpenApi\Schema([
                        'type' => OpenApi\Type::ARRAY,
                        'items' => $this->responseModelResolver->resolveReferenceByClass($type->getCollectionValueTypes()[0]->getClassName()),
                        'nullable' => $type->isNullable(),
                    ])
                ]
            ]
        ]);
    }
}
