<?php

namespace Tests\TestCase\Services\OpenApi\Schema;

use Symfony\Component\HttpFoundation\Response;
use Tests;
use RestApiBundle;

class TypeHintReaderTest extends Tests\TestCase\BaseTestCase
{
    public function testUnsupportedReturnType()
    {
        $reflectionClass = new \ReflectionClass(Tests\TestApp\TestBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('registerAction');

        /** @var RestApiBundle\DTO\Docs\Types\ClassType $returnType */
        $returnType = $this->getTypeHintSchemaReader()->getReturnType($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Types\ClassType::class, $returnType);
        $this->assertSame(Response::class, $returnType->getClass());
        $this->assertFalse($returnType->getNullable());
    }

    public function testEmptyReturnType()
    {
        $reflectionClass = new \ReflectionClass(Tests\TestApp\TestBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('methodWithEmptyTypeHintAction');

        $this->assertNull($this->getTypeHintSchemaReader()->getReturnType($reflectionMethod));
    }

    public function testResponseModelReturnType()
    {
        $reflectionClass = new \ReflectionClass(Tests\TestApp\TestBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('notNullableResponseModelTypeHintAction');

        /** @var RestApiBundle\DTO\Docs\Types\ClassType $returnType */
        $returnType = $this->getTypeHintSchemaReader()->getReturnType($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Types\ClassType::class, $returnType);
        $this->assertSame(Tests\TestApp\TestBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertFalse($returnType->getNullable());
    }

    public function testNullableResponseModelReturnType()
    {
        $reflectionClass = new \ReflectionClass(Tests\TestApp\TestBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('nullableResponseModelTypeHintAction');

        /** @var RestApiBundle\DTO\Docs\Types\ClassType $returnType */
        $returnType = $this->getTypeHintSchemaReader()->getReturnType($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Types\ClassType::class, $returnType);
        $this->assertSame(Tests\TestApp\TestBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertTrue($returnType->getNullable());
    }

    public function testVoidReturnType()
    {
        $reflectionClass = new \ReflectionClass(Tests\TestApp\TestBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('voidReturnTypeAction');

        $returnType = $this->getTypeHintSchemaReader()->getReturnType($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Types\NullType::class, $returnType);
    }
}
