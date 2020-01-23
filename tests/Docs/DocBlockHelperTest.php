<?php

namespace Tests\Docs;

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

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\ReturnType\NullType::class, $returnType);
    }

    public function testSingleResponseModelReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithSingleResponseModelReturnTag');

        /** @var RestApiBundle\DTO\Docs\ReturnType\ClassType $returnType */
        $returnType = $this->getDocBlockHelper()->getReturnTypeByReturnTag($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\ReturnType\ClassType::class, $returnType);
        $this->assertSame(Tests\DemoApp\DemoBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertFalse($returnType->getIsNullable());
    }

    public function testNullableSingleResponseModelReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithNullableSingleResponseModelReturnTag');

        /** @var RestApiBundle\DTO\Docs\ReturnType\ClassType $returnType */
        $returnType = $this->getDocBlockHelper()->getReturnTypeByReturnTag($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\ReturnType\ClassType::class, $returnType);
        $this->assertSame(Tests\DemoApp\DemoBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertTrue($returnType->getIsNullable());
    }

    public function testArrayOfResponseModelsReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithArrayOfResponseModelsReturnTag');

        /** @var RestApiBundle\DTO\Docs\ReturnType\CollectionOfClassesType $returnType */
        $returnType = $this->getDocBlockHelper()->getReturnTypeByReturnTag($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\ReturnType\CollectionOfClassesType::class, $returnType);
        $this->assertSame(Tests\DemoApp\DemoBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertFalse($returnType->getIsNullable());
    }

    public function testNullableArrayOfResponseModelsReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithNullableArrayOfResponseModelsReturnTag');

        /** @var RestApiBundle\DTO\Docs\ReturnType\CollectionOfClassesType $returnType */
        $returnType = $this->getDocBlockHelper()->getReturnTypeByReturnTag($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\ReturnType\CollectionOfClassesType::class, $returnType);
        $this->assertSame(Tests\DemoApp\DemoBundle\ResponseModel\Genre::class, $returnType->getClass());
        $this->assertTrue($returnType->getIsNullable());
    }

    private function getDocBlockHelper(): RestApiBundle\Services\Docs\DocBlockHelper
    {
        $value = $this->getContainer()->get(RestApiBundle\Services\Docs\DocBlockHelper::class);
        if (!$value instanceof RestApiBundle\Services\Docs\DocBlockHelper) {
            throw new \InvalidArgumentException();
        }

        return $value;
    }
}
