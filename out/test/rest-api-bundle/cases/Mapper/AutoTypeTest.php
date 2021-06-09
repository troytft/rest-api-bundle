<?php

class AutoTypeTest extends Tests\BaseTestCase
{
    public function testAllTypesByTypeHints()
    {
        $properties = $this
            ->getSchemaResolver()
            ->resolve(Tests\Fixture\Mapper\AutoType\AllTypesByTypeHints::class)
            ->properties;

        $this->assertCount(7, $properties);

        $string = $properties['string'] ?? null;
        $this->assertTrue($string instanceof RestApiBundle\Model\Mapper\Schema);
        $this->assertTrue($string->isNullable);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\StringTransformer::class, $string->transformerClass);

        $int = $properties['int'] ?? null;
        $this->assertTrue($int instanceof RestApiBundle\Model\Mapper\Schema);
        $this->assertTrue($int->isNullable);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\IntegerTransformer::class, $int->transformerClass);

        $float = $properties['float'] ?? null;
        $this->assertTrue($float instanceof RestApiBundle\Model\Mapper\Schema);
        $this->assertTrue($float->isNullable);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\FloatTransformer::class, $float->transformerClass);

        $bool = $properties['bool'] ?? null;
        $this->assertTrue($bool instanceof RestApiBundle\Model\Mapper\Schema);
        $this->assertTrue($bool->isNullable);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\BooleanTransformer::class, $bool->transformerClass);

        $dateTime = $properties['dateTime'] ?? null;
        $this->assertTrue($dateTime instanceof RestApiBundle\Model\Mapper\Schema);
        $this->assertTrue($dateTime->isNullable);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::class, $dateTime->transformerClass);

        $nestedModel = $properties['nestedModel'] ?? null;
        $this->assertTrue($nestedModel instanceof RestApiBundle\Model\Mapper\Schema);
        $this->assertTrue($string->isNullable);

        $entity = $properties['entity'] ?? null;
        $this->assertTrue($entity instanceof RestApiBundle\Model\Mapper\Schema);
        $this->assertTrue($entity->isNullable);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\EntityTransformer::class, $entity->transformerClass);
    }

    public function testAllTypesByDocBlocks()
    {
        $properties = $this
            ->getSchemaResolver()
            ->resolve(Tests\Fixture\Mapper\AutoType\AllTypesByDocBlocks::class)
            ->properties;

        $this->assertCount(14, $properties);

        $string = $properties['string'] ?? null;
        $this->assertTrue($string instanceof RestApiBundle\Model\Mapper\Schema);
        $this->assertTrue($string->isNullable);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\StringTransformer::class, $string->transformerClass);

        $int = $properties['int'] ?? null;
        $this->assertTrue($int instanceof RestApiBundle\Model\Mapper\Schema);
        $this->assertTrue($int->isNullable);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\IntegerTransformer::class, $int->transformerClass);

        $float = $properties['float'] ?? null;
        $this->assertTrue($float instanceof RestApiBundle\Model\Mapper\Schema);
        $this->assertTrue($float->isNullable);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\FloatTransformer::class, $float->transformerClass);

        $bool = $properties['bool'] ?? null;
        $this->assertTrue($bool instanceof RestApiBundle\Model\Mapper\Schema);
        $this->assertTrue($bool->isNullable);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\BooleanTransformer::class, $bool->transformerClass);

        $dateTime = $properties['dateTime'] ?? null;
        $this->assertTrue($dateTime instanceof RestApiBundle\Model\Mapper\Schema);
        $this->assertTrue($dateTime->isNullable);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::class, $dateTime->transformerClass);

        $nestedModel = $properties['nestedModel'] ?? null;
        $this->assertTrue($nestedModel instanceof RestApiBundle\Model\Mapper\Schema);
        $this->assertTrue($string->isNullable);

        $entity = $properties['entity'] ?? null;
        $this->assertTrue($entity instanceof RestApiBundle\Model\Mapper\Schema);
        $this->assertTrue($entity->isNullable);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\EntityTransformer::class, $entity->transformerClass);

        $strings = $properties['strings'] ?? null;
        $this->assertTrue($strings instanceof RestApiBundle\Model\Mapper\Schema);
        $this->assertTrue($strings->isNullable);
        $this->assertEquals(RestApiBundle\Model\Mapper\Schema::ARRAY_TYPE, $strings->type);

        $ints = $properties['ints'] ?? null;
        $this->assertTrue($ints instanceof RestApiBundle\Model\Mapper\Schema);
        $this->assertTrue($ints->isNullable);
        $this->assertEquals(RestApiBundle\Model\Mapper\Schema::ARRAY_TYPE, $ints->type);

        $floats = $properties['floats'] ?? null;
        $this->assertTrue($floats instanceof RestApiBundle\Model\Mapper\Schema);
        $this->assertTrue($floats->isNullable);
        $this->assertEquals(RestApiBundle\Model\Mapper\Schema::ARRAY_TYPE, $floats->type);

        $bools = $properties['bools'] ?? null;
        $this->assertTrue($bools instanceof RestApiBundle\Model\Mapper\Schema);
        $this->assertTrue($bools->isNullable);
        $this->assertEquals(RestApiBundle\Model\Mapper\Schema::ARRAY_TYPE, $bools->type);

        $dateTimes = $properties['dateTimes'] ?? null;
        $this->assertTrue($dateTimes instanceof RestApiBundle\Model\Mapper\Schema);
        $this->assertTrue($dateTimes->isNullable);
        $this->assertEquals(RestApiBundle\Model\Mapper\Schema::ARRAY_TYPE, $dateTimes->type);

        $nestedModels = $properties['nestedModels'] ?? null;
        $this->assertTrue($nestedModels instanceof RestApiBundle\Model\Mapper\Schema);
        $this->assertTrue($nestedModels->isNullable);
        $this->assertEquals(RestApiBundle\Model\Mapper\Schema::ARRAY_TYPE, $nestedModels->type);

        $entities = $properties['entities'] ?? null;
        $this->assertTrue($entities instanceof RestApiBundle\Model\Mapper\Schema);
        $this->assertTrue($entities->isNullable);
        $this->assertEquals(RestApiBundle\Model\Mapper\Schema::TRANSFORMER_AWARE_TYPE, $entities->type);
    }

    private function getSchemaResolver(): RestApiBundle\Services\Mapper\SchemaResolver
    {
        return $this->getContainer()->get(RestApiBundle\Services\Mapper\SchemaResolver::class);
    }
}
