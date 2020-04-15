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
        $objectType = $this
            ->getResponseModelSchemaReader()
            ->getSchemaByClass(Tests\TestApp\TestBundle\ResponseModel\Genre::class, false);

        $this->assertSame(['id', 'slug', '__typename',], array_keys($objectType->getProperties()));
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\IntegerType::class, $objectType->getProperties()['id']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $objectType->getProperties()['slug']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $objectType->getProperties()['__typename']);
        $this->assertFalse($objectType->getNullable());
    }

    public function testNullableSingleResponseModelNormalization()
    {
        $objectType = $this
            ->getResponseModelSchemaReader()
            ->getSchemaByClass(Tests\TestApp\TestBundle\ResponseModel\Genre::class, false);

        $this->assertSame(['id', 'slug', '__typename',], array_keys($objectType->getProperties()));
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\IntegerType::class, $objectType->getProperties()['id']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $objectType->getProperties()['slug']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $objectType->getProperties()['__typename']);
        $this->assertTrue($objectType->getNullable());
    }

    public function testModelWithDateTime()
    {
        $objectType = $this
            ->getResponseModelSchemaReader()
            ->getSchemaByClass(Tests\TestApp\TestBundle\ResponseModel\TestModelWithDateTime::class, false);

        $this->assertSame(['dateTime', '__typename',], array_keys($objectType->getProperties()));
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\DateTimeType::class, $objectType->getProperties()['dateTime']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $objectType->getProperties()['__typename']);
        $this->assertFalse($objectType->getNullable());
    }

    public function testModelWithDocBlock()
    {
        $objectType = $this
            ->getResponseModelSchemaReader()
            ->getSchemaByClass(Tests\TestApp\TestBundle\ResponseModel\ModelWithDocBlock::class, false);

        $this->assertSame([
            'stringField',
            'nullableStringField',
            'dateTimeField',
            '__typename',
        ], array_keys($objectType->getProperties()));
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\DateTimeType::class, $objectType->getProperties()['dateTime']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $objectType->getProperties()['__typename']);
        $this->assertFalse($objectType->getNullable());
    }
}
