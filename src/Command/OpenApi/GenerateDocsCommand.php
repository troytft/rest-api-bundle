<?php

namespace RestApiBundle\Command\OpenApi;

use RestApiBundle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Filesystem\Filesystem;

use function sprintf;

class GenerateDocsCommand extends Command
{
    private const ARGUMENT_INPUT = 'input';
    private const ARGUMENT_OUTPUT = 'output';
    private const OPTION_TEMPLATE = 'template';
    private const OPTION_YAML = 'yaml';
    private const OPTION_EXCLUDE_PATH = 'exclude-path';

    protected static $defaultName = 'rest-api:generate-docs';

    private RestApiBundle\Services\OpenApi\EndpointFinder $endpointFinder;
    private RestApiBundle\Services\OpenApi\SpecificationGenerator $specificationGenerator;

    public function __construct(
        RestApiBundle\Services\OpenApi\SpecificationGenerator $specificationGenerator
    ) {
        $this->endpointFinder = new RestApiBundle\Services\OpenApi\EndpointFinder();
        $this->specificationGenerator = $specificationGenerator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument(static::ARGUMENT_INPUT, InputArgument::REQUIRED, 'Path to directory with controllers')
            ->addArgument(static::ARGUMENT_OUTPUT, InputArgument::REQUIRED, 'Path to output file')
            ->addOption(static::OPTION_TEMPLATE, null, InputOption::VALUE_REQUIRED, 'Path to template file')
            ->addOption(static::OPTION_YAML, null, InputOption::VALUE_NONE, 'Use yaml specification format, instead json')
            ->addOption(static::OPTION_EXCLUDE_PATH, null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Exclude files from search by string or regular expression');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputDirectory = $input->getArgument(static::ARGUMENT_INPUT);
        $outputFile = $input->getArgument(static::ARGUMENT_OUTPUT);
        $templateFile = $input->getOption(static::OPTION_TEMPLATE);
        $excludePath = $input->getOption(static::OPTION_EXCLUDE_PATH);

        try {
            $endpoints = $this->endpointFinder->findInDirectory($inputDirectory, $excludePath);

            if ($input->getOption(static::OPTION_YAML)) {
                $content = $this->specificationGenerator->generateYaml($endpoints, $templateFile);
            } else {
                $content = $this->specificationGenerator->generateJson($endpoints, $templateFile);
            }

            $filesystem = new Filesystem();
            $filesystem->dumpFile($outputFile, $content);
        } catch (RestApiBundle\Exception\OpenApi\ContextAwareExceptionInterface $exception) {
            $output->writeln(sprintf(
                'Error: %s; Context: %s',
                $exception->getContext(),
                $exception->getMessage()
            ));

            return 1;
        }

        return 0;
    }
}
