<?php

namespace RestApiBundle\Command;

use RestApiBundle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

final class GenerateDocumentationCommand extends Command
{
    private const ARGUMENT_INPUT = 'input';
    private const ARGUMENT_OUTPUT = 'output';
    private const OPTION_TEMPLATE = 'template';
    private const OPTION_EXCLUDE_PATH = 'exclude-path';

    protected static $defaultName = 'rest-api:generate-documentation';

    public function __construct(
        private RestApiBundle\Services\OpenApi\EndpointFinder $endpointFinder,
        private RestApiBundle\Services\OpenApi\SchemaSerializer $fileAdapter,
        private RestApiBundle\Services\OpenApi\SchemaGenerator $schemaGenerator,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument(static::ARGUMENT_INPUT, InputArgument::REQUIRED, 'Path to directory with controllers')
            ->addArgument(static::ARGUMENT_OUTPUT, InputArgument::REQUIRED, 'Path to output file')
            ->addOption(static::OPTION_TEMPLATE, null, InputOption::VALUE_REQUIRED, 'Path to template file')
            ->addOption(static::OPTION_EXCLUDE_PATH, null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Exclude files from search by string or regular expression');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputDirectory = $input->getArgument(static::ARGUMENT_INPUT);
        $outputFile = $input->getArgument(static::ARGUMENT_OUTPUT);
        $templateFile = $input->getOption(static::OPTION_TEMPLATE);
        $excludePath = $input->getOption(static::OPTION_EXCLUDE_PATH);

        $endpoints = $this->endpointFinder->findInDirectory($inputDirectory, $excludePath);

        if ($templateFile) {
            $template = $this->fileAdapter->read($templateFile);
        } else {
            $template = null;
        }

        try {
            $schema = $this->schemaGenerator->generate($endpoints, $template);
        } catch (RestApiBundle\Exception\ContextAware\ContextAwareExceptionInterface $exception) {
            $output->writeln([
                'An error occurred:',
                $exception->getMessage(),
            ]);

            return 1;
        }

        $this->fileAdapter->write($schema, $outputFile);

        return 0;
    }
}
