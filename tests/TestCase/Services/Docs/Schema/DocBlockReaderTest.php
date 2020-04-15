<?php

namespace Tests\TestCase\Services\Docs\Schema;

use Tests;
use RestApiBundle;

class DocBlockReaderTest extends Tests\TestCase\BaseTestCase
{
    /**
     * @var \ReflectionClass
     */
    private $reflectionClass;

    public function __construct()
    {
        parent::__construct();

        $this->reflectionClass = new \ReflectionClass(Tests\TestApp\TestBundle\Controller\DemoController::class);
    }

    public function testMethodWithoutReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithoutReturnTag');

        $this->assertNull($this->getDocBlockSchemaReader()->getMethodReturnSchema($reflectionMethod));
    }

    public function testMethodWithNullReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithNullReturnTag');
        $returnType = $this->getDocBlockSchemaReader()->getMethodReturnSchema($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\NullType::class, $returnType);
    }

    public function testSingleResponseModelReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithSingleResponseModelReturnTag');

        /** @var RestApiBundle\DTO\Docs\Schema\ClassType $returnType */
        $returnType = $this->getDocBlockSchemaReader()->getMethodReturnSchema($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\ClassType::class, $returnType);
        $this->assertSame(Tests\TestApp\TestBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertFalse($returnType->getNullable());
    }

    public function testNullableSingleResponseModelReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithNullableSingleResponseModelReturnTag');

        /** @var RestApiBundle\DTO\Docs\Schema\ClassType $returnType */
        $returnType = $this->getDocBlockSchemaReader()->getMethodReturnSchema($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\ClassType::class, $returnType);
        $this->assertSame(Tests\TestApp\TestBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertTrue($returnType->getNullable());
    }

    public function testArrayOfResponseModelsReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithArrayOfResponseModelsReturnTag');

        /** @var RestApiBundle\DTO\Docs\Schema\ArrayType $returnType */
        $returnType = $this->getDocBlockSchemaReader()->getMethodReturnSchema($reflectionMethod);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\ArrayType::class, $returnType);
        $this->assertFalse($returnType->getNullable());

        /** @var RestApiBundle\DTO\Docs\Schema\ClassType $innerType */
        $innerType = $returnType->getInnerType();
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\ClassType::class, $innerType);
        $this->assertSame(Tests\TestApp\TestBundle\ResponseModel\Genre::class, $innerType->getClass());
        $this->assertFalse($innerType->getNullable());
    }

    public function testNullableArrayOfResponseModelsReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithNullableArrayOfResponseModelsReturnTag');

        /** @var RestApiBundle\DTO\Docs\Schema\ArrayType $returnType */
        $returnType = $this->getDocBlockSchemaReader()->getMethodReturnSchema($reflectionMethod);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\ArrayType::class, $returnType);
        $this->assertTrue($returnType->getNullable());

        /** @var RestApiBundle\DTO\Docs\Schema\ClassType $innerType */
        $innerType = $returnType->getInnerType();
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\ClassType::class, $innerType);
        $this->assertSame(Tests\TestApp\TestBundle\ResponseModel\Genre::class, $innerType->getClass());
        $this->assertFalse($innerType->getNullable());
    }
}
