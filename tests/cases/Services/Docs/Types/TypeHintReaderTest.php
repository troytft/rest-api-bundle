<?php

use Symfony\Component\HttpFoundation\Response;

class TypeHintReaderTest extends Tests\BaseTestCase
{
    public function testUnsupportedReturnType()
    {
        $reflectionClass = new \ReflectionClass(TestApp\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('registerAction');

        /** @var RestApiBundle\DTO\Docs\Types\ClassType $returnType */
        $returnType = $this->getTypeHintSchemaReader()->resolveReturnType($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Types\ClassType::class, $returnType);
        $this->assertSame(Response::class, $returnType->getClass());
        $this->assertFalse($returnType->getNullable());
    }

    public function testEmptyReturnType()
    {
        $reflectionClass = new \ReflectionClass(TestApp\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('methodWithEmptyTypeHintAction');

        $this->assertNull($this->getTypeHintSchemaReader()->resolveReturnType($reflectionMethod));
    }

    public function testResponseModelReturnType()
    {
        $reflectionClass = new \ReflectionClass(TestApp\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('notNullableResponseModelTypeHintAction');

        /** @var RestApiBundle\DTO\Docs\Types\ClassType $returnType */
        $returnType = $this->getTypeHintSchemaReader()->resolveReturnType($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Types\ClassType::class, $returnType);
        $this->assertSame(TestApp\ResponseModel\Book::class, $returnType->getClass());
        $this->assertFalse($returnType->getNullable());
    }

    public function testNullableResponseModelReturnType()
    {
        $reflectionClass = new \ReflectionClass(TestApp\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('nullableResponseModelTypeHintAction');

        /** @var RestApiBundle\DTO\Docs\Types\ClassType $returnType */
        $returnType = $this->getTypeHintSchemaReader()->resolveReturnType($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Types\ClassType::class, $returnType);
        $this->assertSame(TestApp\ResponseModel\Book::class, $returnType->getClass());
        $this->assertTrue($returnType->getNullable());
    }

    public function testVoidReturnType()
    {
        $reflectionClass = new \ReflectionClass(TestApp\Controller\DemoController::class);
        $reflectionMethod = $reflectionClass->getMethod('voidReturnTypeAction');

        $returnType = $this->getTypeHintSchemaReader()->resolveReturnType($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Types\NullType::class, $returnType);
    }

    private function getTypeHintSchemaReader(): RestApiBundle\Services\Docs\Types\TypeHintTypeReader
    {
        return $this->getContainer()->get(RestApiBundle\Services\Docs\Types\TypeHintTypeReader::class);
    }
}
