<?php

namespace RestApiBundle\Services\Docs\OpenApi;

use RestApiBundle;
use cebe\openapi\spec as OpenApi;
use function strtolower;

class RootSchemaResolver
{
    /**
     * @var RestApiBundle\Services\Docs\OpenApi\TypeToSchemaConverter
     */
    private $typeToSchemaConverter;

    public function __construct(RestApiBundle\Services\Docs\OpenApi\TypeToSchemaConverter $typeToSchemaConverter)
    {
        $this->typeToSchemaConverter = $typeToSchemaConverter;
    }

    /**
     * @param RestApiBundle\DTO\Docs\RouteData[] $routeDataItems
     * @return OpenApi\OpenApi
     */
    public function resolve(array $routeDataItems): OpenApi\OpenApi
    {
        $root = new OpenApi\OpenApi([
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Open API Specification',
                'version' => '1.0.0',
            ],
            'paths' => [],
        ]);

        foreach ($routeDataItems as $routeData) {
            $returnType = $routeData->getReturnType();

            $responses = new OpenApi\Responses([]);

            if ($returnType->getIsNullable()) {
                $responses->addResponse('204', new OpenApi\Response(['description' => 'Success response with empty body']));
            }

            if (!$returnType instanceof RestApiBundle\DTO\Docs\Type\NullType) {
                $responses->addResponse('200', new OpenApi\Response([
                    'description' => 'Success response with body',
                    'content' => [
                        'application/json' => [
                            'schema' => $this->typeToSchemaConverter->convert($returnType)
                        ]
                    ]
                ]));
            }

            $operation = new OpenApi\Operation([
                'summary' => $routeData->getTitle(),
                'responses' => $responses,
            ]);

            $parameters = [];

            foreach ($routeData->getPathParameters() as $routeDataPathParameter) {
                $pathParameter = new OpenApi\Parameter([
                    'in' => 'path',
                    'name' => $routeDataPathParameter->getName(),
                    'required' => true,
                ]);

                if ($routeDataPathParameter->getType()) {
                    $pathParameter->schema = $this->typeToSchemaConverter->convert($routeDataPathParameter->getType());
                }

                $parameters[] = $pathParameter;
            }

            if ($parameters) {
                $operation->parameters = $parameters;
            }

            if ($routeData->getTags()) {
                $operation->tags = $routeData->getTags();
            }

            if ($routeData->getDescription()) {
                $operation->description = $routeData->getDescription();
            }

            $pathItem = new OpenApi\PathItem([]);
            foreach ($routeData->getMethods() as $method) {
                $method = strtolower($method);
                $pathItem->{$method} = $operation;
            }

            $root->paths->addPath($routeData->getPath(), $pathItem);
        }

        return $root;
    }
}
