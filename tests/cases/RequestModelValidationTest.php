<?php

class RequestModelValidationTest extends Tests\BaseTestCase
{
    public function testValidationException()
    {
        try {
            $model = new TestApp\RequestModel\ModelWithValidation();
            $this->getRequestModelManager()->handle($model, [
                'stringField' => 's',
                'modelField' => [
                    'stringField' => 's',
                ],
                'collectionField' => [
                    [
                        'stringField' => 's',
                    ]
                ],
                'collectionOfIntegers' => [1, 12, 5],
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $value = $exception->getProperties();
            $this->assertCount(6, $value);

            $this->assertArrayHasKey('stringField', $value);
            $this->assertSame([
                'This value is too short. It should have 6 characters or more.',
                'This value is not a valid email address.',
            ], $value['stringField']);

            $this->assertArrayHasKey('modelField.stringField', $value);
            $this->assertSame([
                'This value is too short. It should have 3 characters or more.',
            ], $value['modelField.stringField']);

            $this->assertArrayHasKey('collectionField.0.stringField', $value);
            $this->assertSame([
                'This value is too short. It should have 3 characters or more.',
            ], $value['collectionField.0.stringField']);

            $this->assertArrayHasKey('collectionOfIntegers.0', $value);
            $this->assertSame([
                'This value should be 10 or more.'
            ], $value['collectionOfIntegers.0']);

            $this->assertArrayHasKey('collectionOfIntegers.2', $value);
            $this->assertSame([
                'This value should be 10 or more.'
            ], $value['collectionOfIntegers.2']);

            $this->assertArrayHasKey('*', $value);
            $this->assertSame([
                'Example message without property',
            ], $value['*']);
        }
    }
}
