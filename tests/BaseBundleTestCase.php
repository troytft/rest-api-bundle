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

    public function __construct()
    {
        parent::__construct();

        $this->bootKernel();
    }

    protected function getBundleClass()
    {
        return RestApiBundle\RestApiBundle::class;
    }

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

    protected function getResponseModelSerializer(): RestApiBundle\Manager\ResponseModel\ResponseModelSerializer
    {
        return $this->getContainer()->get(RestApiBundle\Manager\ResponseModel\ResponseModelSerializer::class);
    }

    protected function getRequestModelManager(): RestApiBundle\Manager\RequestModel\RequestModelManager
    {
        return $this->getContainer()->get(RestApiBundle\Manager\RequestModel\RequestModelManager::class);
    }
}
