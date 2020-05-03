<?php

namespace RestApiBundle\Command\Docs;

use RestApiBundle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use function in_array;
use function sprintf;

class GenerateDocsCommand extends Command
{
    private const ARGUMENT_INPUT = 'input';
    private const ARGUMENT_OUTPUT = 'output';
    private const OPTION_FORMAT = 'format';

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
            ->addArgument(static::ARGUMENT_INPUT, InputArgument::REQUIRED, 'Path to directory with controllers.')
            ->addArgument(static::ARGUMENT_OUTPUT, InputArgument::REQUIRED, 'Path to specification file.')
            ->addOption(static::OPTION_FORMAT, null, InputOption::VALUE_REQUIRED, 'File format json or yaml.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputDirectory = $input->getArgument(static::ARGUMENT_INPUT);
        $outputFile = $input->getArgument(static::ARGUMENT_OUTPUT);
        $format = $input->getOption(static::OPTION_FORMAT) ?? RestApiBundle\Enum\Docs\Format::YAML;

        if (!in_array($format, [RestApiBundle\Enum\Docs\Format::YAML, RestApiBundle\Enum\Docs\Format::JSON], true)) {
            $output->writeln('Invalid format.');

            return 1;
        }

        try {
            $this->docsGenerator->writeToFile($inputDirectory, $outputFile, $format);
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
