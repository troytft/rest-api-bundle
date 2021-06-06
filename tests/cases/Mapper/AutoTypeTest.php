<?php

class AutoTypeTest extends Tests\BaseTestCase
{
    public function testAllSupportedTypes()
    {
        $properties = $this
            ->getSchemaResolver()
            ->resolve(Tests\Fixture\Mapper\AutoType\AllSupportedTypes::class)
            ->getProperties();

        $this->assertCount(14, $properties);

        $string = $properties['string'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $string);
        $this->assertFalse($string->getIsNullable());
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\StringTransformer::class, $string->getTransformerClass());

        $nullableString = $properties['nullableString'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $nullableString);
        $this->assertTrue($nullableString->getIsNullable());
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\StringTransformer::class, $nullableString->getTransformerClass());

        $int = $properties['int'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $int);
        $this->assertFalse($int->getIsNullable());
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\IntegerTransformer::class, $int->getTransformerClass());

        $nullableInt = $properties['nullableInt'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $nullableInt);
        $this->assertTrue($nullableInt->getIsNullable());
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\IntegerTransformer::class, $nullableInt->getTransformerClass());

        $float = $properties['float'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $float);
        $this->assertFalse($float->getIsNullable());
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\FloatTransformer::class, $float->getTransformerClass());

        $nullableFloat = $properties['nullableFloat'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $nullableFloat);
        $this->assertTrue($nullableFloat->getIsNullable());
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\FloatTransformer::class, $nullableFloat->getTransformerClass());

        $bool = $properties['bool'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $bool);
        $this->assertFalse($bool->getIsNullable());
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\BooleanTransformer::class, $bool->getTransformerClass());

        $nullableBool = $properties['nullableBool'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $nullableBool);
        $this->assertTrue($nullableBool->getIsNullable());
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\BooleanTransformer::class, $nullableBool->getTransformerClass());

        $dateTime = $properties['dateTime'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $dateTime);
        $this->assertFalse($dateTime->getIsNullable());
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::class, $dateTime->getTransformerClass());

        $nullableDateTime = $properties['nullableDateTime'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $nullableDateTime);
        $this->assertTrue($nullableDateTime->getIsNullable());
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::class, $nullableDateTime->getTransformerClass());

        $nestedModel = $properties['nestedModel'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $nestedModel);
        $this->assertFalse($nestedModel->getIsNullable());

        $nullableNestedModel = $properties['nullableNestedModel'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $nullableNestedModel);
        $this->assertTrue($nullableNestedModel->getIsNullable());

        $entity = $properties['entity'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $entity);
        $this->assertFalse($entity->getIsNullable());
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\EntityTransformer::class, $entity->getTransformerClass());

        $nullableEntity = $properties['nullableEntity'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $nullableEntity);
        $this->assertTrue($nullableEntity->getIsNullable());
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\EntityTransformer::class, $nullableEntity->getTransformerClass());
    }

    private function getSchemaResolver(): RestApiBundle\Services\Mapper\SchemaResolver
    {
        return $this->getContainer()->get(RestApiBundle\Services\Mapper\SchemaResolver::class);
    }
}
