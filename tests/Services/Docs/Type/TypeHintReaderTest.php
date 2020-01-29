<?php

namespace Tests\Services\Docs\Type;

use Symfony\Component\HttpFoundation\Response;
use Tests;
use RestApiBundle;

class TypeHintReaderTest extends Tests\BaseBundleTestCase
{
    public function testUnsupportedReturnType()
    {
        $reflectionClass = new \ReflectionClass(Tests\TestApp\TestBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('registerAction');

        /** @var RestApiBundle\DTO\Docs\Type\ClassType $returnType */
        $returnType = $this->getReflectionHelper()->getReturnTypeByReflectionMethod($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\ClassType::class, $returnType);
        $this->assertSame(Response::class, $returnType->getClass());
        $this->assertFalse($returnType->getIsNullable());
    }

    public function testEmptyReturnType()
    {
        $reflectionClass = new \ReflectionClass(Tests\TestApp\TestBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('methodWithEmptyTypeHintAction');

        $this->assertNull($this->getReflectionHelper()->getReturnTypeByReflectionMethod($reflectionMethod));
    }

    public function testResponseModelReturnType()
    {
        $reflectionClass = new \ReflectionClass(Tests\TestApp\TestBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('notNullableResponseModelTypeHintAction');

        /** @var RestApiBundle\DTO\Docs\Type\ClassType $returnType */
        $returnType = $this->getReflectionHelper()->getReturnTypeByReflectionMethod($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\ClassType::class, $returnType);
        $this->assertSame(Tests\TestApp\TestBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertFalse($returnType->getIsNullable());
    }

    public function testNullableResponseModelReturnType()
    {
        $reflectionClass = new \ReflectionClass(Tests\TestApp\TestBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('nullableResponseModelTypeHintAction');

        /** @var RestApiBundle\DTO\Docs\Type\ClassType $returnType */
        $returnType = $this->getReflectionHelper()->getReturnTypeByReflectionMethod($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\ClassType::class, $returnType);
        $this->assertSame(Tests\TestApp\TestBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertTrue($returnType->getIsNullable());
    }

    private function getReflectionHelper(): RestApiBundle\Services\Docs\Type\TypeHintReader
    {
        /** @var RestApiBundle\Services\Docs\Type\TypeHintReader $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\Type\TypeHintReader::class);

        return $result;
    }
}
