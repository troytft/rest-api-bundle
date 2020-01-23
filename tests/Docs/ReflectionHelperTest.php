<?php

namespace Tests\Docs\OpenApi;

use Tests;
use RestApiBundle;

class ReflectionHelperTest extends Tests\BaseBundleTestCase
{
    public function testUnsupportedClassException()
    {
        $reflectionClass = new \ReflectionClass(Tests\DemoApp\DemoBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('registerAction');

        try {
            $this->getReflectionHelper()->getReturnTypeByTypeHint($reflectionMethod);
            $this->fail();
        } catch (RestApiBundle\Exception\Docs\ValidationException $validationException) {
            $this->assertSame('Unsupported return type.', $validationException->getMessage());
        }
    }

    private function getReflectionHelper(): RestApiBundle\Services\Docs\ReflectionHelper
    {
        $value = $this->getContainer()->get(RestApiBundle\Services\Docs\ReflectionHelper::class);
        if (!$value instanceof RestApiBundle\Services\Docs\ReflectionHelper) {
            throw new \InvalidArgumentException();
        }

        return $value;
    }
}
