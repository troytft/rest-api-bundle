<?php

namespace Tests;

use RestApiBundle;
use Symfony\Bundle\FrameworkBundle\Tests\Functional\app\AppKernel;

abstract class BaseBundleTestCase extends \Nyholm\BundleTest\BaseBundleTestCase
{
    /**
     * @var AppKernel
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
        $this->kernel->addBundle(Demo\DemoBundle::class);
        $this->kernel->addConfigFile(__DIR__ . '/config.yaml');

        return $this->kernel;
    }

    public function getKernel(): AppKernel
    {
        return $this->kernel;
    }
}
