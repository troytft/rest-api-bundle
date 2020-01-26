<?php

namespace RestApiBundle\Command;

use RestApiBundle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use function sprintf;

class GenerateDocsCommand extends Command
{
    private const OUTPUT_OPTION = 'output';
    private const CONTROLLER_NAMESPACE_PREFIX_OPTION = 'controller-namespace-prefix';

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
            ->addOption(static::OUTPUT_OPTION, null, InputOption::VALUE_REQUIRED, 'Path to output file.')
            ->addOption(static::CONTROLLER_NAMESPACE_PREFIX_OPTION, null, InputOption::VALUE_REQUIRED, 'Prefix for controller namespace.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if (!$input->getOption(static::OUTPUT_OPTION)) {
            $output
                ->writeln('Output file option not specified.');

            return 100;
        }

        try {
            $routeDataItems = $this->routeDataExtractor->getItems($input->getOption(static::CONTROLLER_NAMESPACE_PREFIX_OPTION));
        } catch (RestApiBundle\Exception\Docs\InvalidDefinitionException $invalidDefinitionException) {
            $io->error(sprintf(
                "Error: %s\nController: %s\nAction: %s",
                $invalidDefinitionException->getOriginalErrorMessage(),
                $invalidDefinitionException->getControllerClass(),
                $invalidDefinitionException->getActionName()
            ));

            return 100;
        }

        $rootSchema = $this->openApiRootSchemaResolver->resolve($routeDataItems);

        \cebe\openapi\Writer::writeToYamlFile($rootSchema, $input->getOption(static::OUTPUT_OPTION));

        return 0;
    }
}
