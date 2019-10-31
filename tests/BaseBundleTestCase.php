<?php

namespace Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use RestApiBundle;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class BaseBundleTestCase extends \Nyholm\BundleTest\BaseBundleTestCase
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    protected function getBundleClass()
    {
        return RestApiBundle\RestApiBundle::class;
    }


    public function __construct()
    {
        parent::__construct();

        $this->bootKernel();
    }

    protected function getRequestModelManager(): RestApiBundle\Manager\RequestModelManager
    {
        return $this->getContainer()->get(RestApiBundle\Manager\RequestModelManager::class);
    }

    /**
     * {@inheritDoc}
     */
    protected function createKernel()
    {
        $this->kernel = parent::createKernel();
        $this->kernel->setRootDir(__DIR__ . '/Mock');
        $this->kernel->addBundle(Mock\DemoBundle\DemoBundle::class);
        $this->kernel->addBundle(DoctrineBundle::class);
        $this->kernel->addConfigFile(__DIR__ . '/Mock/config/config.yaml');

        return $this->kernel;
    }

    public function getKernel(): KernelInterface
    {
        return $this->kernel;
    }
}
