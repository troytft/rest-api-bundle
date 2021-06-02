<?php

class AutoTypeTest extends Tests\BaseTestCase
{
    public function testAllSupportedTypes()
    {
        $properties = $this
            ->getSchemaResolver()
            ->resolveByClass(Tests\Fixture\Mapper\AutoType\AllSupportedTypes::class)
            ->getProperties();

        $this->assertCount(14, $properties);

        $string = $properties['string'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\ScalarType::class, $string);
        $this->assertFalse($string->getNullable());
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\StringTransformer::class, $string->getTransformerClass());

        $nullableString = $properties['nullableString'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\ScalarType::class, $nullableString);
        $this->assertTrue($nullableString->getNullable());
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\StringTransformer::class, $nullableString->getTransformerClass());

        $int = $properties['int'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\ScalarType::class, $int);
        $this->assertFalse($int->getNullable());
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\IntegerTransformer::class, $int->getTransformerClass());

        $nullableInt = $properties['nullableInt'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\ScalarType::class, $nullableInt);
        $this->assertTrue($nullableInt->getNullable());
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\IntegerTransformer::class, $nullableInt->getTransformerClass());

        $float = $properties['float'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\ScalarType::class, $float);
        $this->assertFalse($float->getNullable());
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\FloatTransformer::class, $float->getTransformerClass());

        $nullableFloat = $properties['nullableFloat'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\ScalarType::class, $nullableFloat);
        $this->assertTrue($nullableFloat->getNullable());
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\FloatTransformer::class, $nullableFloat->getTransformerClass());

        $bool = $properties['bool'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\ScalarType::class, $bool);
        $this->assertFalse($bool->getNullable());
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\BooleanTransformer::class, $bool->getTransformerClass());

        $nullableBool = $properties['nullableBool'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\ScalarType::class, $nullableBool);
        $this->assertTrue($nullableBool->getNullable());
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\BooleanTransformer::class, $nullableBool->getTransformerClass());

        $dateTime = $properties['dateTime'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\ScalarType::class, $dateTime);
        $this->assertFalse($dateTime->getNullable());
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::class, $dateTime->getTransformerClass());

        $nullableDateTime = $properties['nullableDateTime'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\ScalarType::class, $nullableDateTime);
        $this->assertTrue($nullableDateTime->getNullable());
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::class, $nullableDateTime->getTransformerClass());

        $nestedModel = $properties['nestedModel'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\ObjectType::class, $nestedModel);
        $this->assertFalse($nestedModel->getNullable());

        $nullableNestedModel = $properties['nullableNestedModel'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\ObjectType::class, $nullableNestedModel);
        $this->assertTrue($nullableNestedModel->getNullable());

        $entity = $properties['entity'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\ScalarType::class, $entity);
        $this->assertFalse($entity->getNullable());
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\EntityTransformer::class, $entity->getTransformerClass());

        $nullableEntity = $properties['nullableEntity'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\ScalarType::class, $nullableEntity);
        $this->assertTrue($nullableEntity->getNullable());
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\EntityTransformer::class, $nullableEntity->getTransformerClass());
    }

    private function getSchemaResolver(): RestApiBundle\Services\Mapper\SchemaResolver
    {
        return $this->getContainer()->get(RestApiBundle\Services\Mapper\SchemaResolver::class);
    }
}
