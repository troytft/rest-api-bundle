<?php

namespace RestApiBundle\Command;

use RestApiBundle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

class GenerateDocsCommand extends Command
{
    private const OUTPUT_OPTION = 'output';

    protected static $defaultName = 'rest-api:generate-docs';

    /**
     * @var RestApiBundle\Services\Docs\DocsGenerator
     */
    private $docsGenerator;

    public function __construct(RestApiBundle\Services\Docs\DocsGenerator $docsGenerator)
    {
        $this->docsGenerator = $docsGenerator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addOption(static::OUTPUT_OPTION, 'o', InputOption::VALUE_REQUIRED, 'Path to output file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption(static::OUTPUT_OPTION)) {
            $output
                ->writeln('Output file option not specified.');

            return 100;
        }

        $this->docsGenerator->writeToFile($input->getOption(static::OUTPUT_OPTION));

        return 0;
    }
}
