<?php

namespace Tests\Services\Docs\Type;

use Tests;
use RestApiBundle;
use function array_keys;

class ResponseModelReaderTest extends Tests\BaseBundleTestCase
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

    public function testSingleResponseModelNormalization()
    {
        $classType = new RestApiBundle\DTO\Docs\Type\ClassType(Tests\TestApp\TestBundle\ResponseModel\Genre::class, false);
        $objectType = $this->getResponseModelReader()->resolveObjectTypeByClassType($classType);

        $this->assertSame(['id', 'slug', '__typename',], array_keys($objectType->getProperties()));
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\IntegerType::class, $objectType->getProperties()['id']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\StringType::class, $objectType->getProperties()['slug']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\StringType::class, $objectType->getProperties()['__typename']);
        $this->assertFalse($objectType->getIsNullable());
    }

    public function testNullableSingleResponseModelNormalization()
    {
        $classType = new RestApiBundle\DTO\Docs\Type\ClassType(Tests\TestApp\TestBundle\ResponseModel\Genre::class, true);
        $objectType = $this->getResponseModelReader()->resolveObjectTypeByClassType($classType);

        $this->assertSame(['id', 'slug', '__typename',], array_keys($objectType->getProperties()));
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\IntegerType::class, $objectType->getProperties()['id']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\StringType::class, $objectType->getProperties()['slug']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\StringType::class, $objectType->getProperties()['__typename']);
        $this->assertTrue($objectType->getIsNullable());
    }

    public function testArrayOfResponseModelsNormalization()
    {
        $classesCollectionType = new RestApiBundle\DTO\Docs\Type\ClassesCollectionType(Tests\TestApp\TestBundle\ResponseModel\Genre::class, false);
        $collectionType = $this->getResponseModelReader()->resolveCollectionTypeByClassesCollectionType($classesCollectionType);

        /** @var RestApiBundle\DTO\Docs\Type\ObjectType $collectionInnerType */
        $collectionInnerType = $collectionType->getType();

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\ObjectType::class, $collectionInnerType);
        $this->assertSame(['id', 'slug', '__typename',], array_keys($collectionInnerType->getProperties()));
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\IntegerType::class, $collectionInnerType->getProperties()['id']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\StringType::class, $collectionInnerType->getProperties()['slug']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\StringType::class, $collectionInnerType->getProperties()['__typename']);
        $this->assertFalse($collectionType->getIsNullable());
    }

    public function testNullableArrayOfResponseModelsNormalization()
    {
        $classesCollectionType = new RestApiBundle\DTO\Docs\Type\ClassesCollectionType(Tests\TestApp\TestBundle\ResponseModel\Genre::class, true);
        $collectionType = $this->getResponseModelReader()->resolveCollectionTypeByClassesCollectionType($classesCollectionType);

        /** @var RestApiBundle\DTO\Docs\Type\ObjectType $collectionInnerType */
        $collectionInnerType = $collectionType->getType();

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\ObjectType::class, $collectionInnerType);
        $this->assertSame(['id', 'slug', '__typename',], array_keys($collectionInnerType->getProperties()));
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\IntegerType::class, $collectionInnerType->getProperties()['id']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\StringType::class, $collectionInnerType->getProperties()['slug']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Type\StringType::class, $collectionInnerType->getProperties()['__typename']);
        $this->assertTrue($collectionType->getIsNullable());
    }

    private function getResponseModelReader(): RestApiBundle\Services\Docs\Type\ResponseModelReader
    {
        /** @var RestApiBundle\Services\Docs\Type\ResponseModelReader $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\Type\ResponseModelReader::class);

        return $result;
    }
}
