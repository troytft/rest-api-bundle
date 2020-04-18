<?php

namespace RestApiBundle\Command\Docs;

use RestApiBundle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use function in_array;
use function sprintf;

class GenerateDocsCommand extends Command
{
    private const OPTION_NAMESPACE_FILTER = 'namespace-filter';
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
            ->addOption(static::OPTION_NAMESPACE_FILTER, null, InputOption::VALUE_REQUIRED, 'Prefix for controller namespace filter.')
            ->addOption(static::OPTION_FORMAT, null, InputOption::VALUE_REQUIRED, 'File format json or yaml.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $namespaceFilter = $input->getOption(static::OPTION_NAMESPACE_FILTER);
        $format = $input->getOption(static::OPTION_FORMAT) ?? RestApiBundle\Enum\Docs\Format::YAML;

        if (!in_array($format, [RestApiBundle\Enum\Docs\Format::YAML, RestApiBundle\Enum\Docs\Format::JSON], true)) {
            $output->writeln('Invalid format.');

            return 1;
        }

        try {
            $specification = $this->docsGenerator->generateSpecification($format, $namespaceFilter);
        } catch (RestApiBundle\Exception\Docs\InvalidDefinitionException $exception) {
            $output->writeln(sprintf(
                'Definition error in %s::%s with message "%s" ',
                $exception->getControllerClass(),
                $exception->getActionName(),
                $exception->getOriginalErrorMessage()
            ));

            return 1;
        }

        $output->write($specification);

        return 0;
    }
}
