<?php

namespace RestApiBundle\Services\Docs;

use cebe\openapi\SpecObjectInterface;
use RestApiBundle;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Yaml\Yaml;
use function array_filter;
use function file_put_contents;
use function strpos;

class DocsGenerator
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RestApiBundle\Services\Docs\EndpointDataExtractor
     */
    private $endpointDataFetcher;

    /**
     * @var RestApiBundle\Services\Docs\OpenApi\SchemaGenerator
     */
    private $openApiSchemaGenerator;

    public function __construct(
        RouterInterface $router,
        RestApiBundle\Services\Docs\EndpointDataExtractor $endpointDataFetcher,
        RestApiBundle\Services\Docs\OpenApi\SchemaGenerator $openApiSchemaGenerator
    ) {
        $this->router = $router;
        $this->endpointDataFetcher = $endpointDataFetcher;
        $this->openApiSchemaGenerator = $openApiSchemaGenerator;
    }

    public function generate(string $fileName, ?string $namespaceFilter = null): void
    {
        $items = [];

        foreach ($this->getFilteredRoutes($namespaceFilter) as $route) {
            $endpointData = $this->endpointDataFetcher->extractFromRoute($route);
            if (!$endpointData) {
                continue;
            }

            $items[] = $endpointData;
        }

        $openAPISchema = $this->openApiSchemaGenerator->resolve($items);

        $this->writeSchemaToYamlFile($openAPISchema, $fileName);
    }

    private function writeSchemaToYamlFile(SpecObjectInterface $object, string $fileName)
    {
        $data = Yaml::dump($object->getSerializableData(), 256, 4, Yaml::DUMP_OBJECT_AS_MAP);

        if (file_put_contents($fileName, $data)) {
            throw new \RuntimeException();
        }
    }

    private function getFilteredRoutes(?string $namespaceFilter = null): array
    {
        $routes = $this->router->getRouteCollection()->all();

        if ($namespaceFilter) {
            $routes = array_filter($routes, function (Route $route) use ($namespaceFilter) {
                return strpos($route->getDefault('_controller'), $namespaceFilter) === 0;
            });
        }

        return $routes;
    }
}
