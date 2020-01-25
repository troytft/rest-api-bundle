<?php

namespace Tests\Docs\Type;

use Tests;
use RestApiBundle;
use function array_keys;

class TypeReaderTest extends Tests\BaseBundleTestCase
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

    public function testSingleResponseModelNormalization()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithSingleResponseModelReturnTag');

        /** @var RestApiBundle\DTO\Docs\Type\ObjectType $returnType */
        $returnType = $this->getTypeReader()->getReturnTypeByReflectionMethod($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\ObjectType::class, $returnType);
        $this->assertSame(['id', 'slug', '__typename',], array_keys($returnType->getProperties()));
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\IntegerType::class, $returnType->getProperties()['id']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\StringType::class, $returnType->getProperties()['slug']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\StringType::class, $returnType->getProperties()['__typename']);
        $this->assertFalse($returnType->getIsNullable());
    }

    public function testNullableSingleResponseModelNormalization()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithNullableSingleResponseModelReturnTag');

        /** @var RestApiBundle\DTO\Docs\Type\ObjectType $returnType */
        $returnType = $this->getTypeReader()->getReturnTypeByReflectionMethod($reflectionMethod);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\ObjectType::class, $returnType);
        $this->assertSame(['id', 'slug', '__typename',], array_keys($returnType->getProperties()));
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\IntegerType::class, $returnType->getProperties()['id']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\StringType::class, $returnType->getProperties()['slug']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\StringType::class, $returnType->getProperties()['__typename']);
        $this->assertTrue($returnType->getIsNullable());
    }

    public function testArrayOfResponseModelsNormalization()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithArrayOfResponseModelsReturnTag');

        /** @var RestApiBundle\DTO\Docs\Type\CollectionType $returnType */
        $returnType = $this->getTypeReader()->getReturnTypeByReflectionMethod($reflectionMethod);

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

    public function testNullableArrayOfResponseModelsNormalization()
    {
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithNullableArrayOfResponseModelsReturnTag');

        /** @var RestApiBundle\DTO\Docs\Type\CollectionType $returnType */
        $returnType = $this->getTypeReader()->getReturnTypeByReflectionMethod($reflectionMethod);

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

    private function getTypeReader(): RestApiBundle\Services\Docs\Type\TypeReader
    {
        /** @var RestApiBundle\Services\Docs\Type\TypeReader $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\Type\TypeReader::class);

        return $result;
    }
}
