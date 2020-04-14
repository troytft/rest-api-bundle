<?php

namespace Tests\TestCase\Services\Docs\Schema;

use Tests;
use RestApiBundle;
use function array_keys;

class ResponseModelSchemaReaderTest extends Tests\TestCase\BaseTestCase
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
        $objectType = $this->getResponseModelSchemaReader()->getSchemaByClassType($classType);

        $this->assertSame(['id', 'slug', '__typename',], array_keys($objectType->getProperties()));
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\IntegerType::class, $objectType->getProperties()['id']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $objectType->getProperties()['slug']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $objectType->getProperties()['__typename']);
        $this->assertFalse($objectType->getNullable());
    }

    public function testNullableSingleResponseModelNormalization()
    {
        $classType = new RestApiBundle\DTO\Docs\Schema\ClassType(Tests\TestApp\TestBundle\ResponseModel\Genre::class, true);
        $objectType = $this->getResponseModelSchemaReader()->getSchemaByClassType($classType);

        $this->assertSame(['id', 'slug', '__typename',], array_keys($objectType->getProperties()));
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\IntegerType::class, $objectType->getProperties()['id']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $objectType->getProperties()['slug']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $objectType->getProperties()['__typename']);
        $this->assertTrue($objectType->getNullable());
    }

    public function testArrayOfResponseModelsNormalization()
    {
        $classesCollectionType = new RestApiBundle\DTO\Docs\Schema\ArrayOfClassesType(Tests\TestApp\TestBundle\ResponseModel\Genre::class, false);
        $collectionType = $this->getResponseModelSchemaReader()->getSchemaByArrayOfClassesType($classesCollectionType);

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
        $collectionType = $this->getResponseModelSchemaReader()->getSchemaByArrayOfClassesType($classesCollectionType);

        /** @var RestApiBundle\DTO\Docs\Schema\ObjectType $collectionInnerType */
        $collectionInnerType = $collectionType->getInnerType();

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\ObjectType::class, $collectionInnerType);
        $this->assertSame(['id', 'slug', '__typename',], array_keys($collectionInnerType->getProperties()));
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\IntegerType::class, $collectionInnerType->getProperties()['id']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $collectionInnerType->getProperties()['slug']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $collectionInnerType->getProperties()['__typename']);
        $this->assertTrue($collectionType->getNullable());
    }

    public function testModelWithDateTime()
    {
        $classType = new RestApiBundle\DTO\Docs\Schema\ClassType(Tests\TestApp\TestBundle\ResponseModel\TestModelWithDateTime::class, false);
        $objectType = $this->getResponseModelSchemaReader()->getSchemaByClassType($classType);

        $this->assertSame(['dateTime', '__typename',], array_keys($objectType->getProperties()));
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\DateTimeType::class, $objectType->getProperties()['dateTime']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $objectType->getProperties()['__typename']);
        $this->assertFalse($objectType->getNullable());
    }
}
