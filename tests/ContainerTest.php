<?php

namespace Tests;

use RestApiBundle;
use Nyholm\BundleTest\BaseBundleTestCase;

class ContainerTest extends BaseBundleTestCase
{
    protected function getBundleClass()
    {
        return RestApiBundle\RestApiBundle::class;
    }

    public function testHasRegisteredServices()
    {
        $this->bootKernel();

        $container = $this->getContainer();
        $this->assertTrue($container->has(RestApiBundle\Manager\RequestModelActionArgumentValueResolver::class));
        $this->assertTrue($container->has(RestApiBundle\Manager\RequestModelManager::class));
    }
}
