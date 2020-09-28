<?php

namespace RestApiBundle\Services\OpenApi;

use RestApiBundle;
use Symfony\Component\Filesystem\Filesystem;

class DocsGenerator
{
    /**
     * @var RestApiBundle\Services\OpenApi\EndpointFinder
     */
    private $endpointFinder;

    /**
     * @var RestApiBundle\Services\OpenApi\OpenApi\SpecificationGenerator
     */
    private $openApiSpecificationGenerator;

    public function __construct(
        RestApiBundle\Services\OpenApi\EndpointFinder $endpointFinder,
        RestApiBundle\Services\OpenApi\OpenApi\SpecificationGenerator $openApiSpecificationGenerator
    ) {
        $this->endpointFinder = $endpointFinder;
        $this->openApiSpecificationGenerator = $openApiSpecificationGenerator;
    }

    public function writeToFile(string $controllersDirectory, string $outputFile, string $format): void
    {
        $endpoints = $this->endpointFinder->findInDirectory($controllersDirectory);

        if ($format === RestApiBundle\Enum\Docs\Format::YAML) {
            $content = $this->openApiSpecificationGenerator->generateYaml($endpoints);
        } elseif ($format === RestApiBundle\Enum\Docs\Format::JSON) {
            $content = $this->openApiSpecificationGenerator->generateJson($endpoints);
        } else {
            throw new \InvalidArgumentException();
        }

        $filesystem = new Filesystem();
        $filesystem->dumpFile($outputFile, $content);
    }
}
