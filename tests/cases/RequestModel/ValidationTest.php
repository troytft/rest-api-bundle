<?php

class ValidationTest extends Tests\BaseTestCase
{
    public function testValidationException()
    {
        try {
            $model = new TestApp\RequestModel\ValidationTest\ModelWithValidation();
            $this->getRequestHandler()->handle($model, [
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

    public function testNestedRequestModel()
    {
        $innerRequestModel = new TestApp\RequestModel\ValidationTest\InnerRequestModel();
        $requestModel = new TestApp\RequestModel\ValidationTest\RequestModelWithNestedRequestModel();
        $requestModel->setNestedRequestModel($innerRequestModel);

        $value = $this->getRequestModelValidator()->validate($requestModel);

        $this->assertCount(1, $value);
        $this->assertArrayHasKey('nestedRequestModel.field', $value);
        $this->assertSame(['Invalid value.'], $value['nestedRequestModel.field']);
    }

    public function testNestedArrayOfRequestModels()
    {
        $requestModel = new TestApp\RequestModel\ValidationTest\RequestModelWithNestedArrayOfRequestModels();
        $requestModel->setNestedRequestModels([
            new TestApp\RequestModel\ValidationTest\InnerRequestModel(),
            new TestApp\RequestModel\ValidationTest\InnerRequestModel(),
        ]);

        $value = $this->getRequestModelValidator()->validate($requestModel);

        $this->assertCount(2, $value);
        $this->assertArrayHasKey('nestedRequestModels.0.field', $value);
        $this->assertArrayHasKey('nestedRequestModels.1.field', $value);
        $this->assertSame(['Invalid value.'], $value['nestedRequestModels.0.field']);
        $this->assertSame(['Invalid value.'], $value['nestedRequestModels.1.field']);
    }

    private function getRequestModelValidator(): RestApiBundle\Services\RequestModel\RequestModelValidator
    {
        return $this->getContainer()->get(RestApiBundle\Services\RequestModel\RequestModelValidator::class);
    }

    private function getRequestHandler(): RestApiBundle\Services\RequestModel\RequestModelHandler
    {
        return $this->getContainer()->get(RestApiBundle\Services\RequestModel\RequestModelHandler::class);
    }
}
