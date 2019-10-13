<?php

namespace Tests;

use RestApiBundle;

abstract class BaseBundleTestCase extends \Nyholm\BundleTest\BaseBundleTestCase
{
    /**
     * @var RestApiBundle\Manager\RequestModelManager
     */
    protected $requestModelManager;

    protected function getBundleClass()
    {
        return RestApiBundle\RestApiBundle::class;
    }

    public function __construct()
    {
        parent::__construct();

        $this->bootKernel();
        $container = $this->getContainer();

        $this->requestModelManager = $container->get(RestApiBundle\Manager\RequestModelManager::class);
    }
}
