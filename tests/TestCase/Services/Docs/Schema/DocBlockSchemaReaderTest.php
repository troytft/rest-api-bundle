<?php

namespace Tests\TestCase\Services\Docs\Schema;

use Tests;
use RestApiBundle;

class DocBlockSchemaReaderTest extends Tests\TestCase\BaseBundleTestCase
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

        /** @var RestApiBundle\DTO\Docs\Schema\ArrayOfClassesType $returnType */
        $returnType = $this->getDocBlockSchemaReader()->getMethodReturnSchema($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\ArrayOfClassesType::class, $returnType);
        $this->assertSame(Tests\TestApp\TestBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertFalse($returnType->getNullable());
    }

    public function testNullableArrayOfResponseModelsReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithNullableArrayOfResponseModelsReturnTag');

        /** @var RestApiBundle\DTO\Docs\Schema\ArrayOfClassesType $returnType */
        $returnType = $this->getDocBlockSchemaReader()->getMethodReturnSchema($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\ArrayOfClassesType::class, $returnType);
        $this->assertSame(Tests\TestApp\TestBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertTrue($returnType->getNullable());
    }

    private function getDocBlockSchemaReader(): RestApiBundle\Services\Docs\Schema\DocBlockSchemaReader
    {
        /** @var RestApiBundle\Services\Docs\Schema\DocBlockSchemaReader $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\Schema\DocBlockSchemaReader::class);

        return $result;
    }
}
