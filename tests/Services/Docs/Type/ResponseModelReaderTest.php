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
        $classType = new RestApiBundle\DTO\Docs\Schema\ClassType(Tests\TestApp\TestBundle\ResponseModel\Genre::class, false);
        $objectType = $this->getResponseModelReader()->resolveObjectTypeByClassType($classType);

        $this->assertSame(['id', 'slug', '__typename',], array_keys($objectType->getProperties()));
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\IntegerType::class, $objectType->getProperties()['id']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $objectType->getProperties()['slug']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $objectType->getProperties()['__typename']);
        $this->assertFalse($objectType->getNullable());
    }

    public function testNullableSingleResponseModelNormalization()
    {
        $classType = new RestApiBundle\DTO\Docs\Schema\ClassType(Tests\TestApp\TestBundle\ResponseModel\Genre::class, true);
        $objectType = $this->getResponseModelReader()->resolveObjectTypeByClassType($classType);

        $this->assertSame(['id', 'slug', '__typename',], array_keys($objectType->getProperties()));
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\IntegerType::class, $objectType->getProperties()['id']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $objectType->getProperties()['slug']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $objectType->getProperties()['__typename']);
        $this->assertTrue($objectType->getNullable());
    }

    public function testArrayOfResponseModelsNormalization()
    {
        $classesCollectionType = new RestApiBundle\DTO\Docs\Schema\ArrayOfClassesType(Tests\TestApp\TestBundle\ResponseModel\Genre::class, false);
        $collectionType = $this->getResponseModelReader()->resolveCollectionTypeByClassesCollectionType($classesCollectionType);

        /** @var RestApiBundle\DTO\Docs\Schema\ObjectType $collectionInnerType */
        $collectionInnerType = $collectionType->getInnerType();

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\ObjectType::class, $collectionInnerType);
        $this->assertSame(['id', 'slug', '__typename',], array_keys($collectionInnerType->getProperties()));
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\IntegerType::class, $collectionInnerType->getProperties()['id']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $collectionInnerType->getProperties()['slug']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $collectionInnerType->getProperties()['__typename']);
        $this->assertFalse($collectionType->getNullable());
    }

    public function testNullableArrayOfResponseModelsNormalization()
    {
        $classesCollectionType = new RestApiBundle\DTO\Docs\Schema\ArrayOfClassesType(Tests\TestApp\TestBundle\ResponseModel\Genre::class, true);
        $collectionType = $this->getResponseModelReader()->resolveCollectionTypeByClassesCollectionType($classesCollectionType);

        /** @var RestApiBundle\DTO\Docs\Schema\ObjectType $collectionInnerType */
        $collectionInnerType = $collectionType->getInnerType();

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\ObjectType::class, $collectionInnerType);
        $this->assertSame(['id', 'slug', '__typename',], array_keys($collectionInnerType->getProperties()));
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\IntegerType::class, $collectionInnerType->getProperties()['id']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $collectionInnerType->getProperties()['slug']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $collectionInnerType->getProperties()['__typename']);
        $this->assertTrue($collectionType->getNullable());
    }

    private function getResponseModelReader(): RestApiBundle\Services\Docs\Type\Adapter\ResponseModelReader
    {
        /** @var RestApiBundle\Services\Docs\Type\Adapter\ResponseModelReader $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\Type\Adapter\ResponseModelReader::class);

        return $result;
    }
}
