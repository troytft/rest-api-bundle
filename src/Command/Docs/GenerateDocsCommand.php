<?php

namespace RestApiBundle\Command\Docs;

use RestApiBundle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use function sprintf;

class GenerateDocsCommand extends Command
{
    private const OUTPUT_OPTION = 'output';
    private const TEMPLATE_OPTION = 'template';
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

    /**
     * @var RestApiBundle\Services\Docs\OpenApi\SchemaFileWriter
     */
    private $schemaFileWriter;

    public function __construct(
        RestApiBundle\Services\Docs\RouteDataExtractor $routeDataExtractor,
        RestApiBundle\Services\Docs\OpenApi\RootSchemaResolver $openApiRootSchemaResolver,
        RestApiBundle\Services\Docs\OpenApi\SchemaFileWriter $schemaFileWriter
    ) {
        parent::__construct();

        $this->routeDataExtractor = $routeDataExtractor;
        $this->openApiRootSchemaResolver = $openApiRootSchemaResolver;
        $this->schemaFileWriter = $schemaFileWriter;
    }

    protected function configure()
    {
        $this
            ->addOption(static::OUTPUT_OPTION, null, InputOption::VALUE_REQUIRED, 'Path to output file.')
            ->addOption(static::TEMPLATE_OPTION, null, InputOption::VALUE_REQUIRED, 'Path to template file.')
            ->addOption(static::CONTROLLER_NAMESPACE_PREFIX_OPTION, null, InputOption::VALUE_REQUIRED, 'Prefix for controller namespace.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption(static::OUTPUT_OPTION)) {
            $output
                ->writeln('Output file option not specified.');

            return 1;
        }

        try {
            $routeDataItems = $this->routeDataExtractor->getItems($input->getOption(static::CONTROLLER_NAMESPACE_PREFIX_OPTION));
        } catch (RestApiBundle\Exception\Docs\InvalidDefinitionException $exception) {
            $output->writeln([
                'Invalid Definition Exception',
                sprintf("Message: %s", $exception->getOriginalErrorMessage()),
                sprintf("Controller: %s", $exception->getControllerClass()),
                sprintf("Action: %s", $exception->getActionName()),
            ]);

            return 1;
        }

        $rootSchema = $this->openApiRootSchemaResolver->resolve($routeDataItems);

        $this->schemaFileWriter->writeToYamlFile($rootSchema, $input->getOption(static::OUTPUT_OPTION));

        return 0;
    }
}
