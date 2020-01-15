<?php

namespace RestApiBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;
use function var_dump;

class GenerateDocsCommand extends Command
{
    protected static $defaultName = 'rest-api:generate-docs';

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(ContainerInterface $container)
    {
        var_dump($container->get('router')->getRouteCollection()->all());
//        /** @var $collection \Symfony\Component\Routing\RouteCollection */
//        $collection = $router->getRouteCollection();
//        $allRoutes = $collection->all();
//
//        var_dump($allRoutes);
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        var_dump('sss');
    }
}
