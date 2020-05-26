<?php

namespace RestApiBundle\Services\Docs;

use RestApiBundle;
use Symfony\Component\Filesystem\Filesystem;

class DocsGenerator
{
    /**
     * @var RestApiBundle\Services\Docs\EndpointFinder
     */
    private $endpointFinder;

    /**
     * @var RestApiBundle\Services\Docs\OpenApiSpecificationGenerator
     */
    private $openApiSpecificationGenerator;

    public function __construct(
        RestApiBundle\Services\Docs\EndpointFinder $endpointFinder,
        RestApiBundle\Services\Docs\OpenApiSpecificationGenerator $openApiSpecificationGenerator
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
