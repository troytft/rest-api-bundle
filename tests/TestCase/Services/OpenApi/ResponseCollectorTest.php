<?php

namespace Tests\TestCase\Services\OpenApi;

use Tests;
use RestApiBundle;
use function array_keys;

class ResponseCollectorTest extends Tests\TestCase\BaseTestCase
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
        /** @var RestApiBundle\DTO\OpenApi\Schema\ObjectType $objectType */
        $objectType = $this->invokePrivateMethod($this->getResponseCollector(), 'getResponseModelSchemaByClass', [Tests\TestApp\TestBundle\ResponseModel\ModelWithTypeHint::class, false]);

        $this->assertSame([
            'stringField',
            'nullableStringField',
            'dateTimeField',
            'modelField',
            '__typename',
        ], array_keys($objectType->getProperties()));

        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\StringType::class, $objectType->getProperties()['__typename']);
        $this->assertFalse($objectType->getNullable());

        /** @var RestApiBundle\DTO\OpenApi\Schema\StringType $stringField */
        $stringField = $objectType->getProperties()['stringField'];
        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\StringType::class, $stringField);
        $this->assertFalse($stringField->getNullable());

        /** @var RestApiBundle\DTO\OpenApi\Schema\StringType $nullableStringField */
        $nullableStringField = $objectType->getProperties()['nullableStringField'];
        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\StringType::class, $nullableStringField);
        $this->assertTrue($nullableStringField->getNullable());

        /** @var RestApiBundle\DTO\OpenApi\Schema\DateTimeType $dateTimeField */
        $dateTimeField = $objectType->getProperties()['dateTimeField'];
        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\DateTimeType::class, $dateTimeField);
        $this->assertFalse($dateTimeField->getNullable());

        /** @var RestApiBundle\DTO\OpenApi\Schema\ObjectType $modelField */
        $modelField = $objectType->getProperties()['modelField'];
        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\ObjectType::class, $modelField);
        $this->assertFalse($modelField->getNullable());

        $this->assertSame([
            'stringFieldWithTypeHint',
            'stringFieldWithDocBlock',
            '__typename',
        ], array_keys($modelField->getProperties()));

        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\StringType::class, $modelField->getProperties()['stringFieldWithTypeHint']);
        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\StringType::class, $modelField->getProperties()['stringFieldWithDocBlock']);
        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\StringType::class, $modelField->getProperties()['__typename']);
    }

    public function testModelWithDocBlock()
    {
        /** @var RestApiBundle\DTO\OpenApi\Schema\ObjectType $objectType */
        $objectType = $this->invokePrivateMethod($this->getResponseCollector(), 'getResponseModelSchemaByClass', [Tests\TestApp\TestBundle\ResponseModel\ModelWithDocBlock::class, false]);

        $this->assertSame([
            'stringField',
            'nullableStringField',
            'dateTimeField',
            'modelField',
            'arrayOfModelsField',
            '__typename',
        ], array_keys($objectType->getProperties()));

        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\StringType::class, $objectType->getProperties()['__typename']);
        $this->assertFalse($objectType->getNullable());

        /** @var RestApiBundle\DTO\OpenApi\Schema\StringType $stringField */
        $stringField = $objectType->getProperties()['stringField'];
        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\StringType::class, $stringField);
        $this->assertFalse($stringField->getNullable());

        /** @var RestApiBundle\DTO\OpenApi\Schema\StringType $nullableStringField */
        $nullableStringField = $objectType->getProperties()['nullableStringField'];
        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\StringType::class, $nullableStringField);
        $this->assertTrue($nullableStringField->getNullable());

        /** @var RestApiBundle\DTO\OpenApi\Schema\DateTimeType $dateTimeField */
        $dateTimeField = $objectType->getProperties()['dateTimeField'];
        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\DateTimeType::class, $dateTimeField);
        $this->assertFalse($dateTimeField->getNullable());

        /** @var RestApiBundle\DTO\OpenApi\Schema\ObjectType $modelField */
        $modelField = $objectType->getProperties()['modelField'];
        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\ObjectType::class, $modelField);
        $this->assertFalse($modelField->getNullable());

        $this->assertSame([
            'stringFieldWithTypeHint',
            'stringFieldWithDocBlock',
            '__typename',
        ], array_keys($modelField->getProperties()));

        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\StringType::class, $modelField->getProperties()['stringFieldWithTypeHint']);
        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\StringType::class, $modelField->getProperties()['stringFieldWithDocBlock']);
        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\StringType::class, $modelField->getProperties()['__typename']);

        /** @var RestApiBundle\DTO\OpenApi\Schema\ArrayType $arrayOfModelsField */
        $arrayOfModelsField = $objectType->getProperties()['arrayOfModelsField'];

        /** @var RestApiBundle\DTO\OpenApi\Schema\ObjectType $innerType */
        $innerType = $arrayOfModelsField->getInnerType();
        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\ObjectType::class, $innerType);
        $this->assertFalse($innerType->getNullable());

        $this->assertSame([
            'stringFieldWithTypeHint',
            'stringFieldWithDocBlock',
            '__typename',
        ], array_keys($innerType->getProperties()));

        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\StringType::class, $innerType->getProperties()['stringFieldWithTypeHint']);
        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\StringType::class, $innerType->getProperties()['stringFieldWithDocBlock']);
        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\StringType::class, $innerType->getProperties()['__typename']);
    }

    public function testInvalidDefinationExceptionContext()
    {
        try {
            $this->invokePrivateMethod($this->getResponseCollector(), 'getResponseModelSchemaByClass', [Tests\TestApp\TestBundle\ResponseModel\ModelWithInvalidReturnType::class, false]);
            $this->fail();
        } catch (RestApiBundle\Exception\Docs\InvalidDefinitionException $exception) {
            $this->assertSame("Error: Unsupported return type., Context: Tests\TestApp\TestBundle\ResponseModel\ModelWithInvalidReturnType::getStringField", $exception->getMessage());
        }
    }
}
