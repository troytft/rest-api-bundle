<?php

namespace RestApiBundle\Command;

use RestApiBundle;
use cebe\openapi\spec as OpenApi;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Filesystem\Filesystem;

final class GenerateDocumentationCommand extends Command
{
    private const ARGUMENT_INPUT = 'input';
    private const ARGUMENT_OUTPUT = 'output';
    private const OPTION_TEMPLATE = 'template';
    private const OPTION_EXCLUDE_PATH = 'exclude-path';

    public const YAML_FILE_TYPE = 'yaml';
    public const JSON_FILE_TYPE = 'json';

    protected static $defaultName = 'rest-api:generate-documentation';

    public function __construct(
        private RestApiBundle\Services\OpenApi\EndpointFinder $endpointFinder,
        private RestApiBundle\Services\OpenApi\SchemaSerializer $schemaSerializer,
        private RestApiBundle\Services\OpenApi\SchemaGenerator $schemaGenerator,
        private Filesystem $filesystem,
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
        $endpoints = $this->endpointFinder->findInDirectory(
            $input->getArgument(static::ARGUMENT_INPUT),
            $input->getOption(static::OPTION_EXCLUDE_PATH),
        );

        $template = $input->getOption(static::OPTION_TEMPLATE) ? $this->readFromFile($input->getOption(static::OPTION_TEMPLATE)) : null;

        try {
            $schema = $this->schemaGenerator->generate($endpoints, $template);
        } catch (RestApiBundle\Exception\ContextAware\ContextAwareExceptionInterface $exception) {
            $output->writeln([
                'An error occurred:',
                $exception->getMessage(),
            ]);

            return 1;
        }

        $this->writeToFile($schema, $input->getArgument(static::ARGUMENT_OUTPUT));

        return 0;
    }

    private function readFromFile(string $filename): OpenApi\OpenApi
    {
        $fileType = $this->resolveFileTypeByFilename($filename);
        $fileContent = file_get_contents($filename);

        if ($fileType === static::JSON_FILE_TYPE) {
            $result = $this->schemaSerializer->fromJson($fileContent);
        } elseif ($fileType === static::YAML_FILE_TYPE) {
            $result = $this->schemaSerializer->fromYaml($fileContent);
        } else {
            throw new \LogicException();
        }

        return $result;
    }

    private function writeToFile(OpenApi\OpenApi $openApi, string $filename): void
    {
        $fileType = $this->resolveFileTypeByFilename($filename);

        if ($fileType === static::JSON_FILE_TYPE) {
            $fileContent = $this->schemaSerializer->toJson($openApi);
        } elseif ($fileType === static::YAML_FILE_TYPE) {
            $fileContent = $this->schemaSerializer->toYaml($openApi);
        } else {
            throw new \LogicException();
        }

        $this->filesystem->dumpFile($filename, $fileContent);
    }

    private function resolveFileTypeByFilename(string $filename): string
    {
        return match (pathinfo($filename, \PATHINFO_EXTENSION)) {
            'yml', 'yaml' => static::YAML_FILE_TYPE,
            'json' => static::JSON_FILE_TYPE,
            default => throw new \InvalidArgumentException('Invalid file extension'),
        };
    }
}
