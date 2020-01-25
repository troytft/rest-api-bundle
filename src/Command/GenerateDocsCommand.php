<?php

namespace RestApiBundle\Command;

use RestApiBundle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

class GenerateDocsCommand extends Command
{
    private const OUTPUT_OPTION = 'output';

    protected static $defaultName = 'rest-api:generate-docs';

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
        parent::__construct();

        $this->routeDataExtractor = $routeDataExtractor;
        $this->openApiRootSchemaResolver = $openApiRootSchemaResolver;
    }

    protected function configure()
    {
        $this
            ->addOption(static::OUTPUT_OPTION, 'o', InputOption::VALUE_REQUIRED, 'Path to output file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption(static::OUTPUT_OPTION)) {
            $output
                ->writeln('Output file option not specified.');

            return 100;
        }

        $routeDataItems = $this->routeDataExtractor->getItems();
        $rootSchema = $this->openApiRootSchemaResolver->resolve($routeDataItems);

        \cebe\openapi\Writer::writeToYamlFile($rootSchema, $input->getOption(static::OUTPUT_OPTION));

        return 0;
    }
}
