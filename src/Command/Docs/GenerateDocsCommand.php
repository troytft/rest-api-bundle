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
    private const ARGUMENT_OUTPUT = 'output';
    private const OPTION_NAMESPACE_FILTER = 'namespace-filter';
    private const OPTION_FILE_FORMAT = 'file-format';

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
            ->addOption(static::OPTION_NAMESPACE_FILTER, null, InputOption::VALUE_REQUIRED, 'Prefix for controller namespace filter.')
            ->addOption(static::OPTION_FILE_FORMAT, null, InputOption::VALUE_REQUIRED, 'File format json or yaml.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $outputFileName = $input->getArgument(static::ARGUMENT_OUTPUT);
        $namespaceFilter = $input->getOption(static::OPTION_NAMESPACE_FILTER);
        $fileFormat = $input->getOption(static::OPTION_FILE_FORMAT) ?? RestApiBundle\Enum\Docs\FileFormat::YAML;

        if (!in_array($fileFormat, [RestApiBundle\Enum\Docs\FileFormat::YAML, RestApiBundle\Enum\Docs\FileFormat::JSON], true)) {
            $output->writeln('Invalid file format.');

            return 1;
        }

        try {
            $this->docsGenerator->writeToFile($outputFileName, $fileFormat, $namespaceFilter);
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
