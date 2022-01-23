<?php

class ValidationTest extends Tests\BaseTestCase
{
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
        $model = new Tests\Fixture\Mapper\Movie();

        try {
            $this->getRequestModelHandler()->handle($model, [], $context);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame([
                'name' => ['This value should not be null.'],
                'rating' => ['This value should not be null.'],
            ], $exception->getProperties());
        }

        // disabled
        $context = new RestApiBundle\Model\Mapper\Context(clearMissing: false);
        $model = new Tests\Fixture\Mapper\Movie();

        $this->assertSame('Taxi 2', $model->name);

        $this->getRequestModelHandler()->handle($model, [], $context);
        $this->assertSame('Taxi 2', $model->name);
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

    private function getRequestModelValidator(): RestApiBundle\Services\RequestModel\RequestModelValidator
    {
        return $this->getContainer()->get(RestApiBundle\Services\RequestModel\RequestModelValidator::class);
    }

    private function getRequestModelHandler(): RestApiBundle\Services\RequestModel\RequestModelHandler
    {
        return $this->getContainer()->get(RestApiBundle\Services\RequestModel\RequestModelHandler::class);
    }
}
