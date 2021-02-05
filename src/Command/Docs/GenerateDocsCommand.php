<?php

namespace RestApiBundle\Command\Docs;

use RestApiBundle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Filesystem\Filesystem;

use function in_array;
use function sprintf;

class GenerateDocsCommand extends Command
{
    private const ARGUMENT_INPUT = 'input';
    private const ARGUMENT_OUTPUT = 'output';
    private const OPTION_TEMPLATE = 'template';
    private const OPTION_FORMAT = 'format';

    protected static $defaultName = 'rest-api:generate-docs';

    /**
     * @var RestApiBundle\Services\Docs\EndpointFinder
     */
    private $endpointFinder;

    /**
     * @var RestApiBundle\Services\Docs\OpenApi\SpecificationGenerator
     */
    private $specificationGenerator;

    public function __construct(
        RestApiBundle\Services\Docs\EndpointFinder $endpointFinder,
        RestApiBundle\Services\Docs\OpenApi\SpecificationGenerator $specificationGenerator
    ) {
        $this->endpointFinder = $endpointFinder;
        $this->specificationGenerator = $specificationGenerator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument(static::ARGUMENT_INPUT, InputArgument::REQUIRED, 'Path to directory with controllers')
            ->addArgument(static::ARGUMENT_OUTPUT, InputArgument::REQUIRED, 'Path to output file')
            ->addOption(static::OPTION_TEMPLATE, null, InputOption::VALUE_REQUIRED, 'Path to template file')
            ->addOption(static::OPTION_FORMAT, null, InputOption::VALUE_REQUIRED, 'File format (json|yaml)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputDirectory = $input->getArgument(static::ARGUMENT_INPUT);
        $outputFile = $input->getArgument(static::ARGUMENT_OUTPUT);
        $templateFile = $input->getOption(static::OPTION_TEMPLATE);
        $format = $input->getOption(static::OPTION_FORMAT) ?? RestApiBundle\Enum\Docs\Format::YAML;

        if (!in_array($format, RestApiBundle\Enum\Docs\Format::getValues(), true)) {
            $output->writeln('Invalid file format.');

            return 1;
        }

        try {
            $endpoints = $this->endpointFinder->findInDirectory($inputDirectory);

            if ($format === RestApiBundle\Enum\Docs\Format::YAML) {
                $content = $this->specificationGenerator->generateYaml($endpoints, $templateFile);
            } elseif ($format === RestApiBundle\Enum\Docs\Format::JSON) {
                $content = $this->specificationGenerator->generateJson($endpoints, $templateFile);
            } else {
                throw new \InvalidArgumentException();
            }

            $filesystem = new Filesystem();
            $filesystem->dumpFile($outputFile, $content);
        } catch (RestApiBundle\Exception\Docs\InvalidDefinitionException $exception) {
            $output->writeln(sprintf(
                'Definition error in %s with message "%s"',
                $exception->getContext(),
                $exception->getPrevious()->getMessage()
            ));

            return 1;
        }

        return 0;
    }
}
