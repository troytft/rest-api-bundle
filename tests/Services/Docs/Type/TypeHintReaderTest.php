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

        /** @var RestApiBundle\DTO\Docs\Schema\ClassType $returnType */
        $returnType = $this->getReflectionHelper()->getReturnType($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\ClassType::class, $returnType);
        $this->assertSame(Response::class, $returnType->getClass());
        $this->assertFalse($returnType->getNullable());
    }

    public function testEmptyReturnType()
    {
        $reflectionClass = new \ReflectionClass(Tests\TestApp\TestBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('methodWithEmptyTypeHintAction');

        $this->assertNull($this->getReflectionHelper()->getReturnType($reflectionMethod));
    }

    public function testResponseModelReturnType()
    {
        $reflectionClass = new \ReflectionClass(Tests\TestApp\TestBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('notNullableResponseModelTypeHintAction');

        /** @var RestApiBundle\DTO\Docs\Schema\ClassType $returnType */
        $returnType = $this->getReflectionHelper()->getReturnType($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\ClassType::class, $returnType);
        $this->assertSame(Tests\TestApp\TestBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertFalse($returnType->getNullable());
    }

    public function testNullableResponseModelReturnType()
    {
        $reflectionClass = new \ReflectionClass(Tests\TestApp\TestBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('nullableResponseModelTypeHintAction');

        /** @var RestApiBundle\DTO\Docs\Schema\ClassType $returnType */
        $returnType = $this->getReflectionHelper()->getReturnType($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\ClassType::class, $returnType);
        $this->assertSame(Tests\TestApp\TestBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertTrue($returnType->getNullable());
    }

    private function getReflectionHelper(): RestApiBundle\Services\Docs\Type\Adapter\TypeHintReader
    {
        /** @var RestApiBundle\Services\Docs\Type\Adapter\TypeHintReader $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\Type\Adapter\TypeHintReader::class);

        return $result;
    }
}
