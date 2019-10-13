<?php

namespace Tests;

use RestApiBundle;

abstract class BaseBundleTestCase extends \Nyholm\BundleTest\BaseBundleTestCase
{
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
        $kernel = parent::createKernel();
        $kernel->addConfigFile(__DIR__ . '/config.yaml');

        return $kernel;
    }
}
