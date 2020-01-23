<?php

namespace RestApiBundle\Services\Docs;

use RestApiBundle;

class DocsGenerator
{
    /**
     * @var RestApiBundle\Services\Docs\RouteDataExtractor
     */
    private $routeDataExtractor;

    /**
     * @var RestApiBundle\Services\Docs\OpenApi\RootSchemaResolver
     */
    private $openApiRootSchemaResolver;

    public function __construct(
        RestApiBundle\Services\Docs\RouteDataExtractor $routeDataExtractor,
        RestApiBundle\Services\Docs\OpenApi\RootSchemaResolver $openApiRootSchemaResolver
    ) {
        $this->routeDataExtractor = $routeDataExtractor;
        $this->openApiRootSchemaResolver = $openApiRootSchemaResolver;
    }

    public function writeToFile(string $fileName)
    {
        $routeDataItems = $this->routeDataExtractor->getItems();
        $rootSchema = $this->openApiRootSchemaResolver->resolve($routeDataItems);

        \cebe\openapi\Writer::writeToYamlFile($rootSchema, $fileName);
    }
}
