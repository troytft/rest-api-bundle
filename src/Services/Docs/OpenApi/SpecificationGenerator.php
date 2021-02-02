<?php

namespace RestApiBundle\Services\Docs\OpenApi;

use RestApiBundle;
use cebe\openapi\spec as OpenApi;
use Symfony\Component\Yaml\Yaml;

use function array_merge;
use function array_values;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;
use function ksort;
use function sprintf;
use function strtolower;

class SpecificationGenerator extends RestApiBundle\Services\Docs\OpenApi\AbstractSchemaResolver
{
    /**
     * @var RestApiBundle\Services\Docs\OpenApi\RequestModelResolver
     */
    private $requestModelResolver;

    /**
     * @var RestApiBundle\Services\Docs\OpenApi\ResponseModelResolver
     */
    private $responseModelResolver;

    /**
     * @var RestApiBundle\Services\Docs\OpenApi\DoctrineResolver
     */
    private $doctrineResolver;

    public function __construct(
        RestApiBundle\Services\Docs\OpenApi\RequestModelResolver $requestModelResolver,
        RestApiBundle\Services\Docs\OpenApi\ResponseModelResolver $responseModelResolver,
        RestApiBundle\Services\Docs\OpenApi\DoctrineResolver $doctrineResolver
    ) {
        $this->requestModelResolver = $requestModelResolver;
        $this->responseModelResolver = $responseModelResolver;
        $this->doctrineResolver = $doctrineResolver;
    }

    /**
     * @param RestApiBundle\DTO\Docs\EndpointData[] $endpoints
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
     * @param RestApiBundle\DTO\Docs\EndpointData[] $endpoints
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
     * @param RestApiBundle\DTO\Docs\EndpointData[] $endpointDataItems
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
            'tags' => [],
            'components' => [],
        ]);

        $paths = [];
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

            $response = $routeData->getResponse();

            $responses = new OpenApi\Responses([]);

            if ($response->getNullable()) {
                $responses->addResponse('204', new OpenApi\Response(['description' => 'Success response with empty body']));
            }

            if ($response instanceof RestApiBundle\DTO\Docs\Response\ResponseModel) {
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
            } elseif ($response instanceof RestApiBundle\DTO\Docs\Response\ArrayOfResponseModels) {
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
                if ($pathParameter instanceof RestApiBundle\DTO\Docs\PathParameter\ScalarParameter) {
                    $schema = $this->resolveScalarType(($pathParameter->getType()));
                } elseif ($pathParameter instanceof RestApiBundle\DTO\Docs\PathParameter\EntityTypeParameter) {
                    $schema = $this->doctrineResolver->resolveByColumnType($pathParameter->getClassType()->getClass(), $pathParameter->getFieldName());
                    $schema->description = sprintf('Element by "%s"', $pathParameter->getFieldName());
                    $schema->nullable = $pathParameter->getClassType()->getNullable();

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

            foreach ($routeData->getMethods() as $method) {
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
                $request = $routeData->getRequest();
                if ($request instanceof RestApiBundle\DTO\Docs\Request\RequestModel && $isHttpGetMethod) {
                    $queryParameters = $this->convertRequestModelToParameters($request);
                } elseif ($request instanceof RestApiBundle\DTO\Docs\Request\RequestModel && !$isHttpGetMethod) {
                    $operation->requestBody = $this->convertRequestModelToRequestBody($request);
                }

                if ($pathParameters || $queryParameters) {
                    $operation->parameters = array_merge($pathParameters, $queryParameters);
                }

                if ($routeData->getTags()) {
                    $operation->tags = $routeData->getTags();
                }

                $pathItem->{$method} = $operation;
            }
        }

        ksort($paths);
        $root->paths = new OpenApi\Paths($paths);

        ksort($tags);
        $root->tags = array_values($tags);
        $root->components->schemas = $this->responseModelResolver->dumpSchemas();

        return $root;
    }

    private function convertRequestModelToRequestBody(RestApiBundle\DTO\Docs\Request\RequestModel $requestModel): OpenApi\RequestBody
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
    private function convertRequestModelToParameters(RestApiBundle\DTO\Docs\Request\RequestModel $requestModel): array
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
