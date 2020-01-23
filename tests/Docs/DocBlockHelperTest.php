<?php

namespace Tests\Docs;

use Tests;
use RestApiBundle;
use function array_keys;

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

        /** @var RestApiBundle\DTO\Docs\Type\ObjectType $returnType */
        $returnType = $this->getDocBlockHelper()->getReturnTypeByReturnTag($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\ObjectType::class, $returnType);
        $this->assertSame(['id', 'slug', '__typename',], array_keys($returnType->getProperties()));
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\IntegerType::class, $returnType->getProperties()['id']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\StringType::class, $returnType->getProperties()['slug']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\StringType::class, $returnType->getProperties()['__typename']);
        $this->assertFalse($returnType->getIsNullable());
    }

    public function testNullableSingleResponseModelReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithNullableSingleResponseModelReturnTag');

        /** @var RestApiBundle\DTO\Docs\Type\ObjectType $returnType */
        $returnType = $this->getDocBlockHelper()->getReturnTypeByReturnTag($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\ObjectType::class, $returnType);
        $this->assertSame(['id', 'slug', '__typename',], array_keys($returnType->getProperties()));
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\IntegerType::class, $returnType->getProperties()['id']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\StringType::class, $returnType->getProperties()['slug']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\StringType::class, $returnType->getProperties()['__typename']);
        $this->assertTrue($returnType->getIsNullable());
    }

    public function testArrayOfResponseModelsReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithArrayOfResponseModelsReturnTag');

        /** @var RestApiBundle\DTO\Docs\Type\CollectionType $returnType */
        $returnType = $this->getDocBlockHelper()->getReturnTypeByReturnTag($reflectionMethod);

        /** @var RestApiBundle\DTO\Docs\Type\ObjectType $innerType */
        $innerType = $returnType->getType();

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\CollectionType::class, $returnType);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\ObjectType::class, $innerType);
        $this->assertSame(['id', 'slug', '__typename',], array_keys($innerType->getProperties()));
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\IntegerType::class, $innerType->getProperties()['id']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\StringType::class, $innerType->getProperties()['slug']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\StringType::class, $innerType->getProperties()['__typename']);
        $this->assertFalse($returnType->getIsNullable());
    }

    public function testNullableArrayOfResponseModelsReturnTag()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithNullableArrayOfResponseModelsReturnTag');

        /** @var RestApiBundle\DTO\Docs\Type\CollectionType $returnType */
        $returnType = $this->getDocBlockHelper()->getReturnTypeByReturnTag($reflectionMethod);

        /** @var RestApiBundle\DTO\Docs\Type\ObjectType $innerType */
        $innerType = $returnType->getType();

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\CollectionType::class, $returnType);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\ObjectType::class, $innerType);
        $this->assertSame(['id', 'slug', '__typename',], array_keys($innerType->getProperties()));
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\IntegerType::class, $innerType->getProperties()['id']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\StringType::class, $innerType->getProperties()['slug']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\StringType::class, $innerType->getProperties()['__typename']);
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
