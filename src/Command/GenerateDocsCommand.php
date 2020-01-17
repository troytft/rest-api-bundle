<?php

namespace RestApiBundle\Command;

use RestApiBundle;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;
use function explode;
use function rtrim;
use function var_dump;

class GenerateDocsCommand extends Command
{
    protected static $defaultName = 'rest-api:generate-docs';

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RestApiBundle\Services\Docs\DocsGenerator
     */
    private $docsGenerator;

    public function __construct(ContainerInterface $container, RestApiBundle\Services\Docs\DocsGenerator $docsGenerator)
    {
        $this->router = $container->get('router');
        $this->docsGenerator = $docsGenerator;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->docsGenerator->generate($this->router);
    }
}
