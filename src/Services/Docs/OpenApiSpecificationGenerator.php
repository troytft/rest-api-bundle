<?php

namespace RestApiBundle\Services\Docs;

use RestApiBundle;
use cebe\openapi\spec as OpenApi;
use function array_values;
use function strtolower;

class OpenApiSpecificationGenerator
{
    /**
     * @param RestApiBundle\DTO\Docs\EndpointData[] $endpointDataItems
     * @return OpenApi\OpenApi
     */
    public function generateSpecification(array $endpointDataItems): OpenApi\OpenApi
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
            
            $returnType = $routeData->getResponseSchema();

            $responses = new OpenApi\Responses([]);

            if ($returnType->getNullable()) {
                $responses->addResponse('204', new OpenApi\Response(['description' => 'Success response with empty body']));
            }

            if (!$returnType instanceof RestApiBundle\DTO\Docs\Schema\NullType) {
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

//            foreach ($routeData->getPathParameters() as $routeDataPathParameter) {
//                $pathParameter = new OpenApi\Parameter([
//                    'in' => 'path',
//                    'name' => $routeDataPathParameter->getName(),
//                    'description' => $routeDataPathParameter->getDescription(),
//                    'required' => true,
//                ]);
//
//                if ($routeDataPathParameter->getType()) {
//                    $pathParameter->schema = $this->typeToSchemaConverter->convert($routeDataPathParameter->getType());
//                }
//
//                $parameters[] = $pathParameter;
//            }

//            if ($parameters) {
//                $operation->parameters = $parameters;
//            }

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

        $root->tags = array_values($tags);

        return $root;
    }

    private function convertObjectType(RestApiBundle\DTO\Docs\Schema\ObjectType $objectType): OpenApi\Schema
    {
        $properties = [];

        foreach ($objectType->getProperties() as $key => $propertyType) {
            $properties[$key] = $this->convert($propertyType);
        }

        return new OpenApi\Schema([
            'type' => OpenApi\Type::OBJECT,
            'nullable' => $objectType->getNullable(),
            'properties' => $properties,
        ]);
    }

    private function convertCollectionType(RestApiBundle\DTO\Docs\Schema\ArrayType $collectionType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::ARRAY,
            'nullable' => $collectionType->getNullable(),
            'items' => [
                $this->convert($collectionType->getInnerType())
            ]
        ]);
    }

    private function convertStringType(RestApiBundle\DTO\Docs\Schema\StringType $stringType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::STRING,
            'nullable' => $stringType->getNullable(),
        ]);
    }

    private function convertIntegerType(RestApiBundle\DTO\Docs\Schema\IntegerType $integerType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::INTEGER,
            'nullable' => $integerType->getNullable(),
        ]);
    }

    private function convertFloatType(RestApiBundle\DTO\Docs\Schema\FloatType $floatType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::NUMBER,
            'format' => 'double',
            'nullable' => $floatType->getNullable(),
        ]);
    }

    private function convertBooleanType(RestApiBundle\DTO\Docs\Schema\BooleanType $booleanType): OpenApi\Schema
    {
        return new OpenApi\Schema([
            'type' => OpenApi\Type::BOOLEAN,
            'nullable' => $booleanType->getNullable(),
        ]);
    }
}
