<?php

namespace Tests\Docs\Type;

use Tests;
use RestApiBundle;

class DocBlockHelperTest extends Tests\BaseBundleTestCase
{
    /**
     * @var \ReflectionClass
     */
    private $reflectionClass;

    public function __construct()
    {
        parent::__construct();

        $this->reflectionClass = new \ReflectionClass(Tests\DemoApp\DemoBundle\Controller\DemoController::class);
    }

    public function testMethodWithoutReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithoutReturnTag');

        $this->assertNull($this->getDocBlockHelper()->getReturnTypeByReturnTag($reflectionMethod));
    }

    public function testMethodWithNullReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithNullReturnTag');
        $returnType = $this->getDocBlockHelper()->getReturnTypeByReturnTag($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\NullType::class, $returnType);
    }

    public function testSingleResponseModelReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithSingleResponseModelReturnTag');

        /** @var RestApiBundle\DTO\Docs\Type\ClassType $returnType */
        $returnType = $this->getDocBlockHelper()->getReturnTypeByReturnTag($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\ClassType::class, $returnType);
        $this->assertSame(Tests\DemoApp\DemoBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertFalse($returnType->getIsNullable());
    }

    public function testNullableSingleResponseModelReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithNullableSingleResponseModelReturnTag');

        /** @var RestApiBundle\DTO\Docs\Type\ClassType $returnType */
        $returnType = $this->getDocBlockHelper()->getReturnTypeByReturnTag($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\ClassType::class, $returnType);
        $this->assertSame(Tests\DemoApp\DemoBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertTrue($returnType->getIsNullable());
    }

    public function testArrayOfResponseModelsReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithArrayOfResponseModelsReturnTag');

        /** @var RestApiBundle\DTO\Docs\Type\ClassesCollectionType $returnType */
        $returnType = $this->getDocBlockHelper()->getReturnTypeByReturnTag($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\ClassesCollectionType::class, $returnType);
        $this->assertSame(Tests\DemoApp\DemoBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertFalse($returnType->getIsNullable());
    }

    public function testNullableArrayOfResponseModelsReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithNullableArrayOfResponseModelsReturnTag');

        /** @var RestApiBundle\DTO\Docs\Type\ClassesCollectionType $returnType */
        $returnType = $this->getDocBlockHelper()->getReturnTypeByReturnTag($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\ClassesCollectionType::class, $returnType);
        $this->assertSame(Tests\DemoApp\DemoBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertTrue($returnType->getIsNullable());
    }

    private function getDocBlockHelper(): RestApiBundle\Services\Docs\Type\DocBlockHelper
    {
        /** @var RestApiBundle\Services\Docs\Type\DocBlockHelper $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\Type\DocBlockHelper::class);

        return $result;
    }
}
