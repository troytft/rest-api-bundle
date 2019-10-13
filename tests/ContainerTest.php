<?php

namespace Tests;

use RestApiBundle;

class ContainerTest extends BaseBundleTestCase
{
    public function testHasRegisteredServices()
    {
        $this->assertTrue($this->getContainer()->has(RestApiBundle\Manager\RequestModelActionArgumentValueResolver::class));
        $this->assertTrue($this->getContainer()->has(RestApiBundle\Manager\RequestModelManager::class));
    }
}
