<?php

class AutoTypeTest extends Tests\BaseTestCase
{
    public function testAllSupportedTypes()
    {
        $properties = $this
            ->getSchemaResolver()
            ->resolve(Tests\Fixture\Mapper\AutoType\AllSupportedTypes::class)
            ->properties;

        $this->assertCount(14, $properties);

        $string = $properties['string'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $string);
        $this->assertFalse($string->isNullable);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\StringTransformer::class, $string->transformerClass);

        $nullableString = $properties['nullableString'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $nullableString);
        $this->assertTrue($nullableString->isNullable);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\StringTransformer::class, $nullableString->transformerClass);

        $int = $properties['int'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $int);
        $this->assertFalse($int->isNullable);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\IntegerTransformer::class, $int->transformerClass);

        $nullableInt = $properties['nullableInt'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $nullableInt);
        $this->assertTrue($nullableInt->isNullable);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\IntegerTransformer::class, $nullableInt->transformerClass);

        $float = $properties['float'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $float);
        $this->assertFalse($float->isNullable);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\FloatTransformer::class, $float->transformerClass);

        $nullableFloat = $properties['nullableFloat'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $nullableFloat);
        $this->assertTrue($nullableFloat->isNullable);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\FloatTransformer::class, $nullableFloat->transformerClass);

        $bool = $properties['bool'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $bool);
        $this->assertFalse($bool->isNullable);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\BooleanTransformer::class, $bool->transformerClass);

        $nullableBool = $properties['nullableBool'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $nullableBool);
        $this->assertTrue($nullableBool->isNullable);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\BooleanTransformer::class, $nullableBool->transformerClass);

        $dateTime = $properties['dateTime'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $dateTime);
        $this->assertFalse($dateTime->isNullable);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::class, $dateTime->transformerClass);

        $nullableDateTime = $properties['nullableDateTime'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $nullableDateTime);
        $this->assertTrue($nullableDateTime->isNullable);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::class, $nullableDateTime->transformerClass);

        $nestedModel = $properties['nestedModel'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $nestedModel);
        $this->assertFalse($nestedModel->isNullable);

        $nullableNestedModel = $properties['nullableNestedModel'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $nullableNestedModel);
        $this->assertTrue($nullableNestedModel->isNullable);

        $entity = $properties['entity'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $entity);
        $this->assertFalse($entity->isNullable);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\EntityTransformer::class, $entity->transformerClass);

        $nullableEntity = $properties['nullableEntity'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $nullableEntity);
        $this->assertTrue($nullableEntity->isNullable);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\EntityTransformer::class, $nullableEntity->transformerClass);
    }

    private function getSchemaResolver(): RestApiBundle\Services\Mapper\SchemaResolver
    {
        return $this->getContainer()->get(RestApiBundle\Services\Mapper\SchemaResolver::class);
    }
}
