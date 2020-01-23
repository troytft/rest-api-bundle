<?php

namespace Tests\Docs;

use Tests;
use RestApiBundle;

class TypeHintHelperTest extends Tests\BaseBundleTestCase
{
    public function testUnsupportedReturnType()
    {
        $reflectionClass = new \ReflectionClass(Tests\DemoApp\DemoBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('registerAction');

        try {
            $this->getReflectionHelper()->getReturnTypeByReflectionMethod($reflectionMethod);
            $this->fail();
        } catch (RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException $exception) {
            $this->assertSame('Unsupported return type.', $exception->getMessage());
        }
    }

    public function testEmptyReturnType()
    {
        $reflectionClass = new \ReflectionClass(Tests\DemoApp\DemoBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('methodWithEmptyTypeHintAction');

        $this->assertNull($this->getReflectionHelper()->getReturnTypeByReflectionMethod($reflectionMethod));
    }

    public function testResponseModelReturnType()
    {
        $reflectionClass = new \ReflectionClass(Tests\DemoApp\DemoBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('notNullableResponseModelTypeHintAction');

        /** @var RestApiBundle\DTO\Docs\ReturnType\ObjectType $returnType */
        $returnType = $this->getReflectionHelper()->getReturnTypeByReflectionMethod($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\ReturnType\ObjectType::class, $returnType);
        $this->assertSame(Tests\DemoApp\DemoBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertFalse($returnType->getIsNullable());
    }

    public function testNullableResponseModelReturnType()
    {
        $reflectionClass = new \ReflectionClass(Tests\DemoApp\DemoBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('nullableResponseModelTypeHintAction');

        /** @var RestApiBundle\DTO\Docs\ReturnType\ObjectType $returnType */
        $returnType = $this->getReflectionHelper()->getReturnTypeByReflectionMethod($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\ReturnType\ObjectType::class, $returnType);
        $this->assertSame(Tests\DemoApp\DemoBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertTrue($returnType->getIsNullable());
    }

    private function getReflectionHelper(): RestApiBundle\Services\Docs\TypeHintHelper
    {
        $value = $this->getContainer()->get(RestApiBundle\Services\Docs\TypeHintHelper::class);
        if (!$value instanceof RestApiBundle\Services\Docs\TypeHintHelper) {
            throw new \InvalidArgumentException();
        }

        return $value;
    }
}
