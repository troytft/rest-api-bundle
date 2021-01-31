<?php

class DocBlockReaderTest extends Tests\BaseTestCase
{
    /**
     * @var \ReflectionClass
     */
    private $reflectionClass;

    public function __construct()
    {
        parent::__construct();

        $this->reflectionClass = new \ReflectionClass(TestApp\Controller\DemoController::class);
    }

    public function testMethodWithoutReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithoutReturnTag');

        $this->assertNull($this->getDocBlockSchemaReader()->getReturnType($reflectionMethod));
    }

    public function testMethodWithNullReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithNullReturnTag');
        $returnType = $this->getDocBlockSchemaReader()->getReturnType($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Types\NullType::class, $returnType);
    }

    public function testSingleResponseModelReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithSingleResponseModelReturnTag');

        /** @var RestApiBundle\DTO\Docs\Types\ClassType $returnType */
        $returnType = $this->getDocBlockSchemaReader()->getReturnType($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Types\ClassType::class, $returnType);
        $this->assertSame(TestApp\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertFalse($returnType->getNullable());
    }

    public function testNullableSingleResponseModelReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithNullableSingleResponseModelReturnTag');

        /** @var RestApiBundle\DTO\Docs\Types\ClassType $returnType */
        $returnType = $this->getDocBlockSchemaReader()->getReturnType($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Types\ClassType::class, $returnType);
        $this->assertSame(TestApp\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertTrue($returnType->getNullable());
    }

    public function testArrayOfResponseModelsReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithArrayOfResponseModelsReturnTag');

        /** @var RestApiBundle\DTO\Docs\Types\ArrayType $returnType */
        $returnType = $this->getDocBlockSchemaReader()->getReturnType($reflectionMethod);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Types\ArrayType::class, $returnType);
        $this->assertFalse($returnType->getNullable());

        /** @var RestApiBundle\DTO\Docs\Types\ClassType $innerType */
        $innerType = $returnType->getInnerType();
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Types\ClassType::class, $innerType);
        $this->assertSame(TestApp\ResponseModel\Genre::class, $innerType->getClass());
        $this->assertFalse($innerType->getNullable());
    }

    public function testNullableArrayOfResponseModelsReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithNullableArrayOfResponseModelsReturnTag');

        /** @var RestApiBundle\DTO\Docs\Types\ArrayType $returnType */
        $returnType = $this->getDocBlockSchemaReader()->getReturnType($reflectionMethod);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Types\ArrayType::class, $returnType);
        $this->assertTrue($returnType->getNullable());

        /** @var RestApiBundle\DTO\Docs\Types\ClassType $innerType */
        $innerType = $returnType->getInnerType();
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Types\ClassType::class, $innerType);
        $this->assertSame(TestApp\ResponseModel\Genre::class, $innerType->getClass());
        $this->assertFalse($innerType->getNullable());
    }
}
