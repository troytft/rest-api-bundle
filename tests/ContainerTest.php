<?php

namespace Tests;

use RestApiBundle;

class ContainerTest extends BaseBundleTestCase
{
    public function testHasRegisteredServices()
    {
        $this->assertTrue($this->getContainer()->has(RestApiBundle\Manager\RequestModel\RequestModelManager::class));
    }

    public function testConfig()
    {
        $this->assertFalse($this->getContainer()->getParameter(RestApiBundle\DependencyInjection\ConfigExtension::PARAMETER_REQUEST_MODEL_NULLABLE_BY_DEFAULT));
        $this->assertFalse($this->getContainer()->getParameter(RestApiBundle\DependencyInjection\ConfigExtension::PARAMETER_REQUEST_MODEL_ALLOW_UNDEFINED_KEYS));
        $this->assertTrue($this->getContainer()->getParameter(RestApiBundle\DependencyInjection\ConfigExtension::PARAMETER_REQUEST_MODEL_CLEAR_MISSING));
        $this->assertTrue($this->getContainer()->getParameter(RestApiBundle\DependencyInjection\ConfigExtension::PARAMETER_REQUEST_MODEL_HANDLE_EXCEPTION));
        $this->assertTrue($this->getContainer()->getParameter(RestApiBundle\DependencyInjection\ConfigExtension::PARAMETER_REQUEST_MODEL_DATE_TIME_TRANSFORMER_FORCE_LOCAL_TIMEZONE));
        $this->assertSame('Y-m-d\TH:i:sP', $this->getContainer()->getParameter(RestApiBundle\DependencyInjection\ConfigExtension::PARAMETER_REQUEST_MODEL_DATE_TIME_TRANSFORMER_DEFAULT_FORMAT));
        $this->assertSame('Y-m-d', $this->getContainer()->getParameter(RestApiBundle\DependencyInjection\ConfigExtension::PARAMETER_REQUEST_MODEL_DATE_TRANSFORMER_DEFAULT_FORMAT));
    }
}
