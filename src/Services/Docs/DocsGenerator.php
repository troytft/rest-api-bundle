<?php

namespace RestApiBundle\Services\Docs;

use RestApiBundle;

class DocsGenerator
{
    /**
     * @var RestApiBundle\Services\Docs\RouteFinder
     */
    private $routeFinder;

    /**
     * @var RestApiBundle\Services\Docs\EndpointDataExtractor
     */
    private $endpointDataExtractor;

    /**
     * @var RestApiBundle\Services\Docs\OpenApiSpecificationGenerator
     */
    private $openApiSpecificationGenerator;

    public function __construct(
        RestApiBundle\Services\Docs\RouteFinder $routeFinder,
        RestApiBundle\Services\Docs\EndpointDataExtractor $endpointDataExtractor,
        RestApiBundle\Services\Docs\OpenApiSpecificationGenerator $openApiSpecificationGenerator
    ) {
        $this->routeFinder = $routeFinder;
        $this->endpointDataExtractor = $endpointDataExtractor;
        $this->openApiSpecificationGenerator = $openApiSpecificationGenerator;
    }

    public function generateSpecification(string $format, ?string $namespaceFilter = null): string
    {
        $routes = $this->routeFinder->find($namespaceFilter);
        $endpoints = [];

        foreach ($routes as $route) {
            $endpointData = $this->endpointDataExtractor->extractFromRoute($route);
            if (!$endpointData) {
                continue;
            }

            $endpoints[] = $endpointData;
        }

        if ($format === RestApiBundle\Enum\Docs\Format::YAML) {
            $content = $this->openApiSpecificationGenerator->generateYaml($endpoints);
        } elseif ($format === RestApiBundle\Enum\Docs\Format::JSON) {
            $content = $this->openApiSpecificationGenerator->generateJson($endpoints);
        } else {
            throw new \InvalidArgumentException();
        }

        return $content;
    }
}
