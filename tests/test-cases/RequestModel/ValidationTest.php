<?php

class ValidationTest extends Tests\BaseTestCase
{
    public function testValidationException()
    {
        try {
            $model = new TestApp\RequestModel\ValidationTest\ModelWithValidation();
            $this->getRequestModelHandler()->handle($model, [
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

    public function testClearMissing()
    {
        // enabled
        $context = new RestApiBundle\Model\Mapper\Context(clearMissing: true);

        try {
            $this->getMapper()->map(new Tests\Fixture\Mapper\Movie(), [], $context);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\StackedMappingException $exception) {
            $this->assertCount(2, $exception->getExceptions());

            $this->assertInstanceOf(RestApiBundle\Exception\Mapper\MappingValidation\CanNotBeNullException::class, $exception->getExceptions()[0]);
            $this->assertInstanceOf(RestApiBundle\Exception\Mapper\MappingValidation\CanNotBeNullException::class, $exception->getExceptions()[1]);

            $this->assertSame('name', $exception->getExceptions()[0]->getPathAsString());
            $this->assertSame('rating', $exception->getExceptions()[1]->getPathAsString());
        }

        // disabled
        $context = new RestApiBundle\Model\Mapper\Context(clearMissing: false);

        $movie = new Tests\Fixture\Mapper\Movie();
        $this->assertSame('Taxi 2', $movie->name);

        $this->getMapper()->map($movie, [], $context);
        $this->assertSame('Taxi 2', $movie->name);
    }

    public function testUndefinedKey()
    {
        $model = new Tests\Fixture\RequestModel\ValidationTest\TestUndefinedKeyModel();

        try {
            $this->getRequestModelHandler()->handle($model, [
                'keyNotDefinedInModel' => null,
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['keyNotDefinedInModel' => ['The key is not defined in the model.']], $exception->getProperties());
        }
    }

    public function testErrorPropertyPaths()
    {
        $model = new Tests\Fixture\Mapper\Movie();

        // properties inside object
        try {
            $this->getRequestModelHandler()->handle($model, [
                'name' => null,
                'rating' => null,
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame([
                'name' => ['This value should not be null.'],
                'rating' => ['This value should not be null.'],
            ], $exception->getProperties());
        }

        // element of collection
        try {
            $this->getRequestModelHandler()->handle($model, [
                'name' => 'Taxi 3',
                'rating' => 8.3,
                'releases' => [
                    null,
                ]
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame([
                'releases.0' => ['This value should not be null.'],
            ], $exception->getProperties());
        }

        // object inside collection
        try {
            $this->getRequestModelHandler()->handle($model, [
                'name' => 'Taxi 3',
                'rating' => 8.3,
                'releases' => [
                    [
                        'country' => null,
                        'date' => null,
                    ]
                ]
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame([
                'releases.0.country' => ['This value should not be null.'],
                'releases.0.date' => ['This value should not be null.'],
            ], $exception->getProperties());
        }
    }

    private function getMapper(): RestApiBundle\Services\Mapper\Mapper
    {
        return $this->getContainer()->get(RestApiBundle\Services\Mapper\Mapper::class);
    }

    private function getRequestModelValidator(): RestApiBundle\Services\RequestModel\RequestModelValidator
    {
        return $this->getContainer()->get(RestApiBundle\Services\RequestModel\RequestModelValidator::class);
    }

    private function getRequestModelHandler(): RestApiBundle\Services\RequestModel\RequestModelHandler
    {
        return $this->getContainer()->get(RestApiBundle\Services\RequestModel\RequestModelHandler::class);
    }
}
