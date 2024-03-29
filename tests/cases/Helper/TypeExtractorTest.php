<?php

class TypeExtractorTest extends Tests\BaseTestCase
{
    public function testExtractEnum()
    {
        // class
        $enumData = RestApiBundle\Helper\TypeExtractor::extractEnumData(Tests\Fixture\Helper\TypeExtractorTest\StringEnumAsClass::class);
        $this->assertSame(\Symfony\Component\PropertyInfo\Type::BUILTIN_TYPE_STRING, $enumData->type);
        $this->assertSame(['value_5', 'value_10', 'value_100'], $enumData->values);

        $enumData = RestApiBundle\Helper\TypeExtractor::extractEnumData(Tests\Fixture\Helper\TypeExtractorTest\StringEnumAsClassWithValues::class);
        $this->assertSame(\Symfony\Component\PropertyInfo\Type::BUILTIN_TYPE_STRING, $enumData->type);
        $this->assertSame(['value_10', 'value_100'], $enumData->values);

        $enumData = RestApiBundle\Helper\TypeExtractor::extractEnumData(Tests\Fixture\Helper\TypeExtractorTest\IntegerEnumAsClass::class);
        $this->assertSame(\Symfony\Component\PropertyInfo\Type::BUILTIN_TYPE_INT, $enumData->type);
        $this->assertSame([5, 10, 100], $enumData->values);

        $enumData = RestApiBundle\Helper\TypeExtractor::extractEnumData(Tests\Fixture\Helper\TypeExtractorTest\IntegerEnumAsClassWithValues::class);
        $this->assertSame(\Symfony\Component\PropertyInfo\Type::BUILTIN_TYPE_INT, $enumData->type);
        $this->assertSame([10, 100], $enumData->values);

        // backed enum
        $enumData = RestApiBundle\Helper\TypeExtractor::extractEnumData(Tests\Fixture\Helper\TypeExtractorTest\StringEnumAsBackedEnum::class);
        $this->assertSame(\Symfony\Component\PropertyInfo\Type::BUILTIN_TYPE_STRING, $enumData->type);
        $this->assertSame(['value_5', 'value_10', 'value_100'], $enumData->values);

        $enumData = RestApiBundle\Helper\TypeExtractor::extractEnumData(Tests\Fixture\Helper\TypeExtractorTest\IntegerEnumAsBackedEnum::class);
        $this->assertSame(\Symfony\Component\PropertyInfo\Type::BUILTIN_TYPE_INT, $enumData->type);
        $this->assertSame([5, 10, 100], $enumData->values);

        // combined types
        $enumData = RestApiBundle\Helper\TypeExtractor::extractEnumData(Tests\Fixture\Helper\TypeExtractorTest\CombinedTypesEnum::class);
        $this->assertSame(\Symfony\Component\PropertyInfo\Type::BUILTIN_TYPE_STRING, $enumData->type);
        $this->assertSame([5, 'value_10', 'value_100'], $enumData->values);
    }
}
