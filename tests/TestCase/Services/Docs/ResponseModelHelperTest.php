<?php

namespace Tests\TestCase\Services\Docs;

use Tests;
use RestApiBundle;
use function array_keys;

class ResponseModelHelperTest extends Tests\TestCase\BaseTestCase
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

    public function testModelWithTypeHint()
    {
        $objectType = $this
            ->getResponseModelHelper()
            ->getSchemaByClass(Tests\TestApp\TestBundle\ResponseModel\ModelWithTypeHint::class, false);

        $this->assertSame([
            'stringField',
            'nullableStringField',
            'dateTimeField',
            '__typename',
        ], array_keys($objectType->getProperties()));

        /** @var RestApiBundle\DTO\Docs\Schema\StringType $stringField */
        $stringField = $objectType->getProperties()['stringField'];
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $stringField);
        $this->assertFalse($stringField->getNullable());

        /** @var RestApiBundle\DTO\Docs\Schema\StringType $nullableStringField */
        $nullableStringField = $objectType->getProperties()['nullableStringField'];
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $nullableStringField);
        $this->assertTrue($nullableStringField->getNullable());

        /** @var RestApiBundle\DTO\Docs\Schema\DateTimeType $dateTimeField */
        $dateTimeField = $objectType->getProperties()['dateTimeField'];
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\DateTimeType::class, $dateTimeField);
        $this->assertFalse($dateTimeField->getNullable());

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $objectType->getProperties()['__typename']);
        $this->assertFalse($objectType->getNullable());
    }

    public function testModelWithDocBlock()
    {
        $objectType = $this
            ->getResponseModelHelper()
            ->getSchemaByClass(Tests\TestApp\TestBundle\ResponseModel\ModelWithDocBlock::class, false);

        $this->assertSame([
            'stringField',
            'nullableStringField',
            'dateTimeField',
            '__typename',
        ], array_keys($objectType->getProperties()));

        /** @var RestApiBundle\DTO\Docs\Schema\StringType $stringField */
        $stringField = $objectType->getProperties()['stringField'];
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $stringField);
        $this->assertFalse($stringField->getNullable());

        /** @var RestApiBundle\DTO\Docs\Schema\StringType $nullableStringField */
        $nullableStringField = $objectType->getProperties()['nullableStringField'];
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $nullableStringField);
        $this->assertTrue($nullableStringField->getNullable());

        /** @var RestApiBundle\DTO\Docs\Schema\DateTimeType $dateTimeField */
        $dateTimeField = $objectType->getProperties()['dateTimeField'];
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\DateTimeType::class, $dateTimeField);
        $this->assertFalse($dateTimeField->getNullable());

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $objectType->getProperties()['__typename']);
        $this->assertFalse($objectType->getNullable());
    }
}
