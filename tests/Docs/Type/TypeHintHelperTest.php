<?php

namespace Tests\Docs\Type;

use Symfony\Component\HttpFoundation\Response;
use Tests;
use RestApiBundle;

class TypeHintHelperTest extends Tests\BaseBundleTestCase
{
    public function testUnsupportedReturnType()
    {
        $reflectionClass = new \ReflectionClass(Tests\DemoApp\DemoBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('registerAction');

        /** @var RestApiBundle\DTO\Docs\Type\ClassType $returnType */
        $returnType = $this->getReflectionHelper()->getReturnTypeByReflectionMethod($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\ClassType::class, $returnType);
        $this->assertSame(Response::class, $returnType->getClass());
        $this->assertFalse($returnType->getIsNullable());
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

        /** @var RestApiBundle\DTO\Docs\Type\ClassType $returnType */
        $returnType = $this->getReflectionHelper()->getReturnTypeByReflectionMethod($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\ClassType::class, $returnType);
        $this->assertSame(Tests\DemoApp\DemoBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertFalse($returnType->getIsNullable());
    }

    public function testNullableResponseModelReturnType()
    {
        $reflectionClass = new \ReflectionClass(Tests\DemoApp\DemoBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('nullableResponseModelTypeHintAction');

        /** @var RestApiBundle\DTO\Docs\Type\ClassType $returnType */
        $returnType = $this->getReflectionHelper()->getReturnTypeByReflectionMethod($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\ClassType::class, $returnType);
        $this->assertSame(Tests\DemoApp\DemoBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertTrue($returnType->getIsNullable());
    }

    private function getReflectionHelper(): RestApiBundle\Services\Docs\Type\TypeHintHelper
    {
        /** @var RestApiBundle\Services\Docs\Type\TypeHintHelper $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\Type\TypeHintHelper::class);

        return $result;
    }
}
