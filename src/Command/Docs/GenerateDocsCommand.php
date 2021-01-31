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
            ->addArgument(static::ARGUMENT_INPUT, InputArgument::REQUIRED, 'Path to directory with controllers.')
            ->addArgument(static::ARGUMENT_OUTPUT, InputArgument::REQUIRED, 'Path to specification file.')
            ->addOption(static::OPTION_FORMAT, null, InputOption::VALUE_REQUIRED, 'File format json or yaml.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputDirectory = $input->getArgument(static::ARGUMENT_INPUT);
        $outputFile = $input->getArgument(static::ARGUMENT_OUTPUT);
        $format = $input->getOption(static::OPTION_FORMAT) ?? RestApiBundle\Enum\Docs\Format::YAML;

        if (!in_array($format, RestApiBundle\Enum\Docs\Format::getValues(), true)) {
            $output->writeln('Invalid file format.');

            return 1;
        }

        try {
            $this->writeToFile($inputDirectory, $outputFile, $format);
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

    private function writeToFile(string $inputDirectory, string $outputFile, string $format): void
    {
        $endpoints = $this->endpointFinder->findInDirectory($inputDirectory);

        if ($format === RestApiBundle\Enum\Docs\Format::YAML) {
            $content = $this->specificationGenerator->generateYaml($endpoints);
        } elseif ($format === RestApiBundle\Enum\Docs\Format::JSON) {
            $content = $this->specificationGenerator->generateJson($endpoints);
        } else {
            throw new \InvalidArgumentException();
        }

        $filesystem = new Filesystem();
        $filesystem->dumpFile($outputFile, $content);
    }
}
