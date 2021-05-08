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

        $this->assertNull($this->getDocBlockSchemaReader()->resolveReturnType($reflectionMethod));
    }

    public function testMethodWithNullReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithNullReturnTag');
        $returnType = $this->getDocBlockSchemaReader()->resolveReturnType($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\Model\OpenApi\Types\NullType::class, $returnType);
    }

    public function testSingleResponseModelReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithSingleResponseModelReturnTag');

        /** @var RestApiBundle\Model\OpenApi\Types\ClassType $returnType */
        $returnType = $this->getDocBlockSchemaReader()->resolveReturnType($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\Model\OpenApi\Types\ClassType::class, $returnType);
        $this->assertSame(TestApp\ResponseModel\Book::class, $returnType->getClass());
        $this->assertFalse($returnType->getNullable());
    }

    public function testNullableSingleResponseModelReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithNullableSingleResponseModelReturnTag');

        /** @var RestApiBundle\Model\OpenApi\Types\ClassType $returnType */
        $returnType = $this->getDocBlockSchemaReader()->resolveReturnType($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\Model\OpenApi\Types\ClassType::class, $returnType);
        $this->assertSame(TestApp\ResponseModel\Book::class, $returnType->getClass());
        $this->assertTrue($returnType->getNullable());
    }

    public function testArrayOfResponseModelsReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithArrayOfResponseModelsReturnTag');

        /** @var RestApiBundle\Model\OpenApi\Types\ArrayType $returnType */
        $returnType = $this->getDocBlockSchemaReader()->resolveReturnType($reflectionMethod);
        $this->assertInstanceOf(RestApiBundle\Model\OpenApi\Types\ArrayType::class, $returnType);
        $this->assertFalse($returnType->getNullable());

        /** @var RestApiBundle\Model\OpenApi\Types\ClassType $innerType */
        $innerType = $returnType->getInnerType();
        $this->assertInstanceOf(RestApiBundle\Model\OpenApi\Types\ClassType::class, $innerType);
        $this->assertSame(TestApp\ResponseModel\Book::class, $innerType->getClass());
        $this->assertFalse($innerType->getNullable());
    }

    public function testNullableArrayOfResponseModelsReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithNullableArrayOfResponseModelsReturnTag');

        /** @var RestApiBundle\Model\OpenApi\Types\ArrayType $returnType */
        $returnType = $this->getDocBlockSchemaReader()->resolveReturnType($reflectionMethod);
        $this->assertInstanceOf(RestApiBundle\Model\OpenApi\Types\ArrayType::class, $returnType);
        $this->assertTrue($returnType->getNullable());

        /** @var RestApiBundle\Model\OpenApi\Types\ClassType $innerType */
        $innerType = $returnType->getInnerType();
        $this->assertInstanceOf(RestApiBundle\Model\OpenApi\Types\ClassType::class, $innerType);
        $this->assertSame(TestApp\ResponseModel\Book::class, $innerType->getClass());
        $this->assertFalse($innerType->getNullable());
    }

    private function getDocBlockSchemaReader(): RestApiBundle\Services\OpenApi\Types\DocBlockTypeReader
    {
        return $this->getContainer()->get(RestApiBundle\Services\OpenApi\Types\DocBlockTypeReader::class);
    }
}
