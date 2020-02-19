<?php

namespace RestApiBundle\Services\Docs;

use cebe\openapi\SpecObjectInterface;
use RestApiBundle;
use Symfony\Component\Yaml\Yaml;
use function file_put_contents;

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
     * @var RestApiBundle\Services\Docs\OpenApi\SchemaGenerator
     */
    private $openApiSchemaGenerator;

    public function __construct(
        RestApiBundle\Services\Docs\RouteFinder $routeFinder,
        RestApiBundle\Services\Docs\EndpointDataExtractor $endpointDataExtractor,
        RestApiBundle\Services\Docs\OpenApi\SchemaGenerator $openApiSchemaGenerator
    ) {
        $this->routeFinder = $routeFinder;
        $this->endpointDataExtractor = $endpointDataExtractor;
        $this->openApiSchemaGenerator = $openApiSchemaGenerator;
    }

    public function writeToFile(string $fileName, ?string $namespaceFilter = null): void
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

        $openAPISchema = $this->openApiSchemaGenerator->resolve($endpoints);

        $this->writeSchemaToYamlFile($openAPISchema, $fileName);
    }

    private function writeSchemaToYamlFile(SpecObjectInterface $object, string $fileName)
    {
        $data = Yaml::dump($object->getSerializableData(), 256, 4, Yaml::DUMP_OBJECT_AS_MAP);

        if (!file_put_contents($fileName, $data)) {
            throw new \RuntimeException();
        }
    }
}
