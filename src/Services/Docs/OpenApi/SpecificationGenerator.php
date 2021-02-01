<?php

namespace RestApiBundle\Services\Docs\OpenApi;

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
     * @var RestApiBundle\Services\Docs\OpenApi\RequestModelResolver
     */
    private $requestModelResolver;

    /**
     * @var RestApiBundle\Services\Docs\OpenApi\ResponseModelResolver
     */
    private $responseModelResolver;

    public function __construct(
        RestApiBundle\Services\Docs\OpenApi\RequestModelResolver $requestModelResolver,
        RestApiBundle\Services\Docs\OpenApi\ResponseModelResolver $responseModelResolver
    ) {
        $this->requestModelResolver = $requestModelResolver;
        $this->responseModelResolver = $responseModelResolver;
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

            $response = $routeData->getResponse();

            $responses = new OpenApi\Responses([]);

            if ($response->getNullable()) {
                $responses->addResponse('204', new OpenApi\Response(['description' => 'Success response with empty body']));
            }

            if ($response instanceof RestApiBundle\DTO\Docs\Response\ResponseModel) {
                $responses->addResponse('200', new OpenApi\Response([
                    'description' => 'Success response with body',
                    'content' => [
                        'application/json' => [
                            'schema' => $this->responseModelResolver->resolveByClass($response->getClass(), $response->getNullable())
                        ]
                    ]
                ]));
            } elseif ($response instanceof RestApiBundle\DTO\Docs\Response\ArrayOfResponseModels) {
                $responses->addResponse('200', new OpenApi\Response([
                    'description' => 'Success response with body',
                    'content' => [
                        'application/json' => [
                            'schema' => new OpenApi\Schema([
                                'type' => OpenApi\Type::ARRAY,
                                'nullable' => $response->getNullable(),
                                'items' => $this->responseModelResolver->resolveByClass($response->getClass(), false),
                            ])
                        ]
                    ]
                ]));
            }

            $pathParameters = [];

            foreach ($routeData->getPathParameters() as $pathParameter) {
                $pathParameters[] = new OpenApi\Parameter([
                    'in' => 'path',
                    'name' => $pathParameter->getName(),
                    'required' => !$pathParameter->getSchema()->getNullable(),
                    'schema' => $this->convertScalarType($pathParameter->getSchema()),
                ]);
            }

            $pathItem = $root->paths->getPath($routeData->getPath());
            if (!$pathItem) {
                $pathItem = new OpenApi\PathItem([]);
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

            $root->paths->addPath($routeData->getPath(), $pathItem);
        }

        $root->tags = array_values($tags);

        return $root;
    }

    private function convertScalarType(RestApiBundle\DTO\Docs\Types\TypeInterface $scalarType): OpenApi\Schema
    {
        switch (true) {
            case $scalarType instanceof RestApiBundle\DTO\Docs\Types\StringType:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::STRING,
                    'nullable' => $scalarType->getNullable(),
                ]);

                break;

            case $scalarType instanceof RestApiBundle\DTO\Docs\Types\IntegerType:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::INTEGER,
                    'nullable' => $scalarType->getNullable(),
                ]);

                break;

            case $scalarType instanceof RestApiBundle\DTO\Docs\Types\FloatType:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::NUMBER,
                    'format' => 'double',
                    'nullable' => $scalarType->getNullable(),
                ]);

                break;

            case $scalarType instanceof RestApiBundle\DTO\Docs\Types\BooleanType:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::BOOLEAN,
                    'nullable' => $scalarType->getNullable(),
                ]);

                break;

            default:
                throw new \InvalidArgumentException();
        }

        return $result;
    }

    private function convertRequestModelToRequestBody(RestApiBundle\DTO\Docs\Request\RequestModel $requestModel): OpenApi\RequestBody
    {
        return new OpenApi\RequestBody([
            'description' => 'Request body',
            'required' => $requestModel->getNullable(),
            'content' => [
                'application/json' => [
                    'schema' => $this->requestModelResolver->resolveByClass($requestModel->getClass()),
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
                'type' => 'query',
                'name' => $propertyName,
                'required' => !$propertySchema->nullable,
                'schema' => $propertySchema,
            ]);

            // Swagger UI does not show schema description in parameters
            if ($propertySchema->description) {
                $parameter->description = $propertySchema->description;
                $propertySchema->description = null;
            }

            $result[] = $parameter;
        }

        return $result;
    }
}
