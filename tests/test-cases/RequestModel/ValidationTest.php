<?php

class ValidationTest extends Tests\BaseTestCase
{
    public function testNestedValidation()
    {
        $model = new Tests\Fixture\TestCases\RequestModel\ValidationTest\TestNestedValidationModel();

        // child model
        try {
            $this->getRequestModelHandler()->handle($model, [
                'childModel' => [
                    'field' => null,
                ],
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame([
                'childModel.field' => [
                    'This value is not valid.',
                ],
            ], $exception->getProperties());
        }

        // model inside child collection
        try {
            $this->getRequestModelHandler()->handle($model, [
                'childModels' => [
                    [
                        'field' => null,
                    ],
                    [
                        'field' => null,
                    ],
                ],
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame([
                'childModels.0.field' => [
                    'This value is not valid.',
                ],
                'childModels.1.field' => [
                    'This value is not valid.',
                ],
            ], $exception->getProperties());
        }
    }

    public function testClearMissing()
    {
        // enabled
        $model = new Tests\Fixture\Mapper\Movie();
        $context = new RestApiBundle\Model\Mapper\Context(clearMissing: true);

        try {
            $this->getRequestModelHandler()->handle($model, [], $context);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame([
                'name' => [
                    'This value should not be null.',
                ],
                'rating' => [
                    'This value should not be null.',
                ],
            ], $exception->getProperties());
        }

        // disabled
        $model = new Tests\Fixture\Mapper\Movie();
        $context = new RestApiBundle\Model\Mapper\Context(clearMissing: false);

        $this->getRequestModelHandler()->handle($model, [], $context);
        $this->assertSame('Taxi 2', $model->name);
    }

    public function testUndefinedKey()
    {
        $model = new Tests\Fixture\Mapper\Movie();
        $context = new RestApiBundle\Model\Mapper\Context(clearMissing: false);

        try {
            $this->getRequestModelHandler()->handle($model, [
                'releases' => [
                    [
                        'name' => 'Release 1',
                    ]
                ]
            ], $context);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame([
                'releases.0.name' => [
                    'The key is not defined in the model.',
                ],
            ], $exception->getProperties());
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
                'name' => [
                    'This value should not be null.',
                ],
                'rating' => [
                    'This value should not be null.',
                ],
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
                'releases.0' => [
                    'This value should not be null.',
                ],
            ], $exception->getProperties());
        }

        // object inside element of collection
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
                'releases.0.country' => [
                    'This value should not be null.',
                ],
                'releases.0.date' => [
                    'This value should not be null.',
                ],
            ], $exception->getProperties());
        }
    }

    private function getRequestModelHandler(): RestApiBundle\Services\RequestModel\RequestModelHandler
    {
        return $this->getContainer()->get(RestApiBundle\Services\RequestModel\RequestModelHandler::class);
    }
}
