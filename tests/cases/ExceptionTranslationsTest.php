<?php

class ExceptionTranslationsTest extends Tests\BaseTestCase
{
    public function testAll()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithAllTypes();
            $this->getRequestModelManager()->handle($model, [
                'booleanType' => 'string',
                'stringType' => false,
                'integerType' => false,
                'floatType' => false,
                'model' => false,
                'collection' => false,
                'date' => false,
                'dateTime' => false,
                'undefinedKey' => false,
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $expected = [
                'booleanType' => ['This value should be a boolean.'],
                'stringType' => ['This value should be a string.'],
                'integerType' => ['This value should be an integer.'],
                'floatType' => ['This value should be a float.'],
                'model' => ['This value should be an object.'],
                'collection' => ['This value should be a collection.'],
                'date' => ['This value should be valid date with format "Y-m-d".'],
                'dateTime' => ['This value should be valid date time with format "Y-m-d\TH:i:sP".'],
                'undefinedKey' => ['The key is not defined in the model.'],
            ];
            $this->assertSame($expected, $exception->getProperties());
        }
    }

    public function testInvalidDates()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithAllTypes();
            $this->getRequestModelManager()->handle($model, [
                'date' => '2010-03-45',
                'dateTime' => '2010-03-45T10:00:00+00:00',
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $expected = [
                'date' => ['This value is not a valid date.'],
                'dateTime' => ['This value is not a valid datetime.'],
            ];
            $this->assertSame($expected, $exception->getProperties());
        }
    }
}
