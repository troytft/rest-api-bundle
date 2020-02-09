<?php

namespace Tests\Services\Docs\Type;

use Tests;
use RestApiBundle;

class DocBlockReaderTest extends Tests\BaseBundleTestCase
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

        $this->assertNull($this->getDocBlockHelper()->getReturnType($reflectionMethod));
    }

    public function testMethodWithNullReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithNullReturnTag');
        $returnType = $this->getDocBlockHelper()->getReturnType($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\NullType::class, $returnType);
    }

    public function testSingleResponseModelReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithSingleResponseModelReturnTag');

        /** @var RestApiBundle\DTO\Docs\Type\ClassType $returnType */
        $returnType = $this->getDocBlockHelper()->getReturnType($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\ClassType::class, $returnType);
        $this->assertSame(Tests\TestApp\TestBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertFalse($returnType->getNullable());
    }

    public function testNullableSingleResponseModelReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithNullableSingleResponseModelReturnTag');

        /** @var RestApiBundle\DTO\Docs\Type\ClassType $returnType */
        $returnType = $this->getDocBlockHelper()->getReturnType($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\ClassType::class, $returnType);
        $this->assertSame(Tests\TestApp\TestBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertTrue($returnType->getNullable());
    }

    public function testArrayOfResponseModelsReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithArrayOfResponseModelsReturnTag');

        /** @var RestApiBundle\DTO\Docs\Type\ArrayOfClassesType $returnType */
        $returnType = $this->getDocBlockHelper()->getReturnType($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\ArrayOfClassesType::class, $returnType);
        $this->assertSame(Tests\TestApp\TestBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertFalse($returnType->getNullable());
    }

    public function testNullableArrayOfResponseModelsReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithNullableArrayOfResponseModelsReturnTag');

        /** @var RestApiBundle\DTO\Docs\Type\ArrayOfClassesType $returnType */
        $returnType = $this->getDocBlockHelper()->getReturnType($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\ArrayOfClassesType::class, $returnType);
        $this->assertSame(Tests\TestApp\TestBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertTrue($returnType->getNullable());
    }

    private function getDocBlockHelper(): RestApiBundle\Services\Docs\Type\DocBlockReader
    {
        /** @var RestApiBundle\Services\Docs\Type\DocBlockReader $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\Type\DocBlockReader::class);

        return $result;
    }
}
