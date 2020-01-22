<?php

namespace RestApiBundle\Services\Docs;

use cebe\openapi\spec as OpenApi;
use RestApiBundle;
use function lcfirst;
use function strpos;
use function strtolower;
use function substr;

class DocsGenerator
{
    /**
     * @var RestApiBundle\Services\Docs\RouteDataExtractor
     */
    private $routeDataExtractor;

    public function __construct(RestApiBundle\Services\Docs\RouteDataExtractor $routeDataExtractor)
    {
        $this->routeDataExtractor = $routeDataExtractor;
    }

    public function writeToFile(string $fileName)
    {
        $openapiPaths = new OpenApi\Paths([]);
        $openapi = new OpenApi\OpenApi([
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Open API Specification',
                'version' => '1.0.0',
            ],
            'paths' => $openapiPaths,
        ]);

        $routeDataItems = $this->routeDataExtractor->getItems();

        foreach ($routeDataItems as $routeData) {
            $returnType = $routeData->getReturnType();
            if (!$returnType instanceof RestApiBundle\DTO\Docs\ReturnType\ClassType) {
                throw new \InvalidArgumentException('Not implemented.');
            }

            $openapiOperation = new OpenApi\Operation([
                'summary' => $routeData->getTitle(),
                'responses' => [
                    200 => $this->getOpenApiResponseByResponseModelClass($returnType->getClass()),
                ]
            ]);

            if ($routeData->getTags()) {
                $openapiOperation->tags = $routeData->getTags();
            }

            if ($routeData->getDescription()) {
                $openapiOperation->description = $routeData->getDescription();
            }

            $openapiPathItem = new OpenApi\PathItem([]);
            foreach ($routeData->getMethods() as $method) {
                $method = strtolower($method);
                $openapiPathItem->{$method} = $openapiOperation;
            }

            $openapiPaths->addPath($routeData->getPath(), $openapiPathItem);

            //var_dump($annotation);
//            var_dump(, $actionReflectionMethod->getParameters());
            //var_dump($route->getMethods(), $route->getPath(), $route->getDefault('_controller'), $controllerClass, $actionName);
        }

        \cebe\openapi\Writer::writeToYamlFile($openapi, $fileName);
    }

    private function getOpenApiResponseByResponseModelClass(string $class): OpenApi\Response
    {
        $responseModelReflection = new \ReflectionClass($class);
        $methods = $responseModelReflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        $properties = [];

        foreach ($methods as $method) {
            if (strpos($method->getName(), 'get') !== 0) {
                continue;
            }

            $propertyName = lcfirst(substr($method->getName(), 3));

            $returnType = (string) $responseModelReflection->getMethod($method->getName())->getReturnType();

            switch ($returnType) {
                case 'int':
                    $properties[$propertyName] = [
                        'type' => 'number',
                    ];

                    break;

                case 'string':
                    $properties[$propertyName] = [
                        'type' => 'string',
                    ];

                    break;

                default:
                    throw new \InvalidArgumentException('Not implemented.');
            }
        }

        $properties[RestApiBundle\Services\Response\GetSetMethodNormalizer::ATTRIBUTE_TYPENAME] = [
            'type' => 'string',
        ];

        $response = new OpenApi\Response([
            'description' => 'Success',
            'content' => [
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => $properties,
                    ]
                ]
            ]
        ]);

        return $response;
    }
}
