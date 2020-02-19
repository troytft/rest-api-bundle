<?php

namespace Tests\Services\Docs\Schema;

use Symfony\Component\HttpFoundation\Response;
use Tests;
use RestApiBundle;

class TypeHintSchemaReaderTest extends Tests\BaseBundleTestCase
{
    public function testUnsupportedReturnType()
    {
        $reflectionClass = new \ReflectionClass(Tests\TestApp\TestBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('registerAction');

        /** @var RestApiBundle\DTO\Docs\Schema\ClassType $returnType */
        $returnType = $this->getTypeHintSchemaReader()->getFunctionReturnSchema($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\ClassType::class, $returnType);
        $this->assertSame(Response::class, $returnType->getClass());
        $this->assertFalse($returnType->getNullable());
    }

    public function testEmptyReturnType()
    {
        $reflectionClass = new \ReflectionClass(Tests\TestApp\TestBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('methodWithEmptyTypeHintAction');

        $this->assertNull($this->getTypeHintSchemaReader()->getFunctionReturnSchema($reflectionMethod));
    }

    public function testResponseModelReturnType()
    {
        $reflectionClass = new \ReflectionClass(Tests\TestApp\TestBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('notNullableResponseModelTypeHintAction');

        /** @var RestApiBundle\DTO\Docs\Schema\ClassType $returnType */
        $returnType = $this->getTypeHintSchemaReader()->getFunctionReturnSchema($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\ClassType::class, $returnType);
        $this->assertSame(Tests\TestApp\TestBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertFalse($returnType->getNullable());
    }

    public function testNullableResponseModelReturnType()
    {
        $reflectionClass = new \ReflectionClass(Tests\TestApp\TestBundle\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('nullableResponseModelTypeHintAction');

        /** @var RestApiBundle\DTO\Docs\Schema\ClassType $returnType */
        $returnType = $this->getTypeHintSchemaReader()->getFunctionReturnSchema($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\ClassType::class, $returnType);
        $this->assertSame(Tests\TestApp\TestBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertTrue($returnType->getNullable());
    }

    private function getTypeHintSchemaReader(): RestApiBundle\Services\Docs\Schema\TypeHintSchemaReader
    {
        /** @var RestApiBundle\Services\Docs\Schema\TypeHintSchemaReader $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\Schema\TypeHintSchemaReader::class);

        return $result;
    }
}
