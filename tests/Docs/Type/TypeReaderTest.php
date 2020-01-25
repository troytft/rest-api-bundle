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
        $classType = new RestApiBundle\DTO\Docs\Type\ClassType(Tests\DemoApp\DemoBundle\ResponseModel\Genre::class, false);

        /** @var RestApiBundle\DTO\Docs\Type\ObjectType $returnType */
        $returnType = $this->getTypeReader()->normalizeReturnType($classType);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\ObjectType::class, $returnType);
        $this->assertSame(['id', 'slug', '__typename',], array_keys($returnType->getProperties()));
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\IntegerType::class, $returnType->getProperties()['id']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\StringType::class, $returnType->getProperties()['slug']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\StringType::class, $returnType->getProperties()['__typename']);
        $this->assertFalse($returnType->getIsNullable());
    }

    public function testNullableSingleResponseModelNormalization()
    {
        $classType = new RestApiBundle\DTO\Docs\Type\ClassType(Tests\DemoApp\DemoBundle\ResponseModel\Genre::class, true);

        /** @var RestApiBundle\DTO\Docs\Type\ObjectType $returnType */
        $returnType = $this->getTypeReader()->normalizeReturnType($classType);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\ObjectType::class, $returnType);
        $this->assertSame(['id', 'slug', '__typename',], array_keys($returnType->getProperties()));
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\IntegerType::class, $returnType->getProperties()['id']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\StringType::class, $returnType->getProperties()['slug']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\StringType::class, $returnType->getProperties()['__typename']);
        $this->assertTrue($returnType->getIsNullable());
    }

    public function testArrayOfResponseModelsNormalization()
    {
        $classesCollectionType = new RestApiBundle\DTO\Docs\Type\ClassesCollectionType(Tests\DemoApp\DemoBundle\ResponseModel\Genre::class, false);

        /** @var RestApiBundle\DTO\Docs\Type\CollectionType $returnType */
        $returnType = $this->getTypeReader()->normalizeReturnType($classesCollectionType);

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
        $classesCollectionType = new RestApiBundle\DTO\Docs\Type\ClassesCollectionType(Tests\DemoApp\DemoBundle\ResponseModel\Genre::class, true);

        /** @var RestApiBundle\DTO\Docs\Type\CollectionType $returnType */
        $returnType = $this->getTypeReader()->normalizeReturnType($classesCollectionType);

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
