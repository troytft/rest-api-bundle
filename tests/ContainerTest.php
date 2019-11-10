<?php

namespace Tests;

use RestApiBundle;

class ContainerTest extends BaseBundleTestCase
{
    public function testHasRegisteredServices()
    {
        $this->assertTrue($this->getContainer()->has(RestApiBundle\Manager\RequestModel\RequestModelManager::class));
    }
}
