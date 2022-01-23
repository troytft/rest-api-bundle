<?php

class ValidationTest extends Tests\BaseTestCase
{
    public function testNestedValidation()
    {
        $model = new Tests\Fixture\Mapper\ValidationTest\TestNestedValidationModel();

        // nested model
        try {
            $this->getMapper()->map($model, [
                'childModel' => [],
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame([
                'childModel.field' => ['This value is not valid.'],
            ], $exception->getProperties());
        }

        // nested collection of models
        try {
            $this->getMapper()->map($model, [
                'childModels' => [
                    [
                    ],
                    [
                    ],
                ],
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame([
                'childModels.0.field' => ['This value is not valid.'],
                'childModels.1.field' => ['This value is not valid.'],
            ], $exception->getProperties());
        }
    }

    public function testClearMissing()
    {
        // enabled
        $context = new RestApiBundle\Model\Mapper\Context(clearMissing: true);
        $model = new Tests\Fixture\Mapper\Movie();

        try {
            $this->getMapper()->map($model, [], $context);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame([
                'name' => ['This value should not be null.'],
                'rating' => ['This value should not be null.'],
            ], $exception->getProperties());
        }

        // disabled
        $context = new RestApiBundle\Model\Mapper\Context(clearMissing: false);
        $model = new Tests\Fixture\Mapper\Movie();

        $this->assertSame('Taxi 2', $model->name);

        $this->getMapper()->map($model, [], $context);
        $this->assertSame('Taxi 2', $model->name);
    }

    public function testUndefinedKey()
    {
        $model = new Tests\Fixture\Mapper\ValidationTest\TestUndefinedKeyModel();

        try {
            $this->getMapper()->map($model, [
                'keyNotDefinedInModel' => null,
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame(['keyNotDefinedInModel' => ['The key is not defined in the model.']], $exception->getProperties());
        }
    }

    public function testErrorPropertyPaths()
    {
        $model = new Tests\Fixture\Mapper\Movie();

        // properties inside object
        try {
            $this->getMapper()->map($model, [
                'name' => null,
                'rating' => null,
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame([
                'name' => ['This value should not be null.'],
                'rating' => ['This value should not be null.'],
            ], $exception->getProperties());
        }

        // element of collection
        try {
            $this->getMapper()->map($model, [
                'name' => 'Taxi 3',
                'rating' => 8.3,
                'releases' => [
                    null,
                ]
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame([
                'releases.0' => ['This value should not be null.'],
            ], $exception->getProperties());
        }

        // object inside collection
        try {
            $this->getMapper()->map($model, [
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
        } catch (RestApiBundle\Exception\Mapper\MappingException $exception) {
            $this->assertSame([
                'releases.0.country' => ['This value should not be null.'],
                'releases.0.date' => ['This value should not be null.'],
            ], $exception->getProperties());
        }
    }
}
