<?php

namespace Tests;

use RestApiBundle;

class ContainerTest extends BaseBundleTestCase
{
    public function testHasRegisteredServices()
    {
        $container = $this->getContainer();
        $this->assertTrue($container->has(RestApiBundle\Manager\RequestModelActionArgumentValueResolver::class));
        $this->assertTrue($container->has(RestApiBundle\Manager\RequestModelManager::class));
    }
}
