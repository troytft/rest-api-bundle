<?php

namespace RestApiBundle\Services\Docs\OpenApi;

use RestApiBundle;
use cebe\openapi\spec as OpenApi;
use function strtolower;

class RootSchemaResolver
{
    /**
     * @var RestApiBundle\Services\Docs\OpenApi\ReturnTypeSchemaResolver
     */
    private $returnTypeSchemaResolver;

    public function __construct(RestApiBundle\Services\Docs\OpenApi\ReturnTypeSchemaResolver $returnTypeSchemaResolver)
    {
        $this->returnTypeSchemaResolver = $returnTypeSchemaResolver;
    }

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
            if (!$returnType instanceof RestApiBundle\DTO\Docs\ReturnType\ClassType) {
                throw new \InvalidArgumentException('Not implemented.');
            }

            $response = new OpenApi\Response([
                'description' => 'Success',
                'content' => [
                    'application/json' => [
                        'schema' => $this->returnTypeSchemaResolver->resolve($returnType)
                    ]
                ]
            ]);

            $operation = new OpenApi\Operation([
                'summary' => $routeData->getTitle(),
                'responses' => [
                    200 => $response,
                ]
            ]);

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
