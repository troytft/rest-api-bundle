<?php

namespace Tests\Docs\OpenApi;

use Tests;
use RestApiBundle;

class ReflectionHelperTest extends Tests\BaseBundleTestCase
{
    public function testUnsupportedClassTypeHint()
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

    public function testEmptyTypeHint()
    {
        $reflectionClass = new \ReflectionClass(Tests\DemoApp\DemoBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('methodWithEmptyTypeHintAction');

        $this->assertNull($this->getReflectionHelper()->getReturnTypeByTypeHint($reflectionMethod));
    }

    public function testNotNullableResponseModelTypeHint()
    {
        $reflectionClass = new \ReflectionClass(Tests\DemoApp\DemoBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('notNullableResponseModelTypeHintAction');

        /** @var RestApiBundle\DTO\Docs\ReturnType\ClassType $returnType */
        $returnType = $this->getReflectionHelper()->getReturnTypeByTypeHint($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\ReturnType\ClassType::class, $returnType);
        $this->assertSame(Tests\DemoApp\DemoBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertFalse($returnType->getIsNullable());
    }

    public function testNullableResponseModelTypeHint()
    {
        $reflectionClass = new \ReflectionClass(Tests\DemoApp\DemoBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('nullableResponseModelTypeHintAction');

        /** @var RestApiBundle\DTO\Docs\ReturnType\ClassType $returnType */
        $returnType = $this->getReflectionHelper()->getReturnTypeByTypeHint($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\ReturnType\ClassType::class, $returnType);
        $this->assertSame(Tests\DemoApp\DemoBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertTrue($returnType->getIsNullable());
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
