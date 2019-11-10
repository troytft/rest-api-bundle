<?php

namespace Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use RestApiBundle;
use Tests\DemoApp;
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

    protected function getRequestModelManager(): RestApiBundle\Manager\RequestModel\RequestModelManager
    {
        $manager = $this->getContainer()->get(RestApiBundle\Manager\RequestModel\RequestModelManager::class);
        if (!$manager instanceof RestApiBundle\Manager\RequestModel\RequestModelManager) {
            throw new \InvalidArgumentException();
        }

        return $manager;
    }

    /**
     * {@inheritDoc}
     */
    protected function createKernel()
    {
        $this->kernel = parent::createKernel();
        $this->kernel->setRootDir(__DIR__ . '/DemoApp');
        $this->kernel->addBundle(DemoApp\DemoBundle\DemoBundle::class);
        $this->kernel->addBundle(DoctrineBundle::class);
        $this->kernel->addConfigFile(__DIR__ . '/DemoApp/config/config.yaml');

        return $this->kernel;
    }

    public function getKernel(): KernelInterface
    {
        return $this->kernel;
    }
}
