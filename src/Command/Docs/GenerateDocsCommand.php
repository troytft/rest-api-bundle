<?php

namespace RestApiBundle\Command\Docs;

use RestApiBundle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

class GenerateDocsCommand extends Command
{
    private const ARGUMENT_OUTPUT = 'output';
    private const OPTION_NAMESPACE_FILTER = 'namespace-filter';

    protected static $defaultName = 'rest-api:generate-docs';

    /**
     * @var RestApiBundle\Services\Docs\DocsGenerator
     */
    private $docsGenerator;

    public function __construct(RestApiBundle\Services\Docs\DocsGenerator $docsGenerator)
    {
        parent::__construct();

        $this->docsGenerator = $docsGenerator;
    }

    protected function configure()
    {
        $this
            ->addArgument(static::ARGUMENT_OUTPUT, InputArgument::REQUIRED, 'Path to output file.')
            ->addOption(static::OPTION_NAMESPACE_FILTER, null, InputOption::VALUE_REQUIRED, 'Prefix for controller namespace filter.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $outputFileName = $input->getArgument(static::ARGUMENT_OUTPUT);
        $namespaceFilter = $input->getOption(static::OPTION_NAMESPACE_FILTER);

        try {
            $this->docsGenerator->writeToFile($outputFileName, $namespaceFilter);
        } catch (RestApiBundle\Exception\Docs\InvalidDefinitionException $exception) {
            $output->writeln([
                'Invalid Definition Exception',
                sprintf("Message: %s", $exception->getOriginalErrorMessage()),
                sprintf("Controller: %s", $exception->getControllerClass()),
                sprintf("Action: %s", $exception->getActionName()),
            ]);

            return 1;
        }

        return 0;
    }
}
