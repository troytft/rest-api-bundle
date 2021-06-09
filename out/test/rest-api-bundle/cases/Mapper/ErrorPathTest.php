<?php

class ErrorPathTest extends Tests\BaseTestCase
{
    public function testObjectProperty()
    {
        $model = new Tests\Fixture\Mapper\Movie();
        $data = [
            'name' => null,
            'rating' => null,
        ];

        try {
            $this->getMapper()->map($model, $data);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\StackedMappingException $exception) {
            $this->assertCount(2, $exception->getExceptions());

            $this->assertInstanceOf(RestApiBundle\Exception\Mapper\MappingValidation\CanNotBeNullException::class, $exception->getExceptions()[0]);
            $this->assertSame('name', $exception->getExceptions()[0]->getPathAsString());

            $this->assertInstanceOf(RestApiBundle\Exception\Mapper\MappingValidation\CanNotBeNullException::class, $exception->getExceptions()[1]);
            $this->assertSame('rating', $exception->getExceptions()[1]->getPathAsString());
        }
    }

    public function testCollectionItem()
    {
        $model = new Tests\Fixture\Mapper\Movie();
        $data = [
            'name' => 'Taxi 3',
            'rating' => 8.3,
            'releases' => [
                null,
            ]
        ];

        try {
            $this->getMapper()->map($model, $data);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\StackedMappingException $exception) {
            $this->assertCount(1, $exception->getExceptions());

            $this->assertInstanceOf(RestApiBundle\Exception\Mapper\MappingValidation\CanNotBeNullException::class, $exception->getExceptions()[0]);
            $this->assertSame('releases.0', $exception->getExceptions()[0]->getPathAsString());
        }
    }

    public function testObjectPropertyInsideCollection()
    {
        $model = new Tests\Fixture\Mapper\Movie();
        $data = [
            'name' => 'Taxi 3',
            'rating' => 8.3,
            'releases' => [
                [
                    'country' => null,
                    'date' => null,
                ]
            ]
        ];

        try {
            $this->getMapper()->map($model, $data);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\StackedMappingException $exception) {
            $this->assertCount(2, $exception->getExceptions());

            $this->assertInstanceOf(RestApiBundle\Exception\Mapper\MappingValidation\CanNotBeNullException::class, $exception->getExceptions()[0]);
            $this->assertSame('releases.0.country', $exception->getExceptions()[0]->getPathAsString());

            $this->assertInstanceOf(RestApiBundle\Exception\Mapper\MappingValidation\CanNotBeNullException::class, $exception->getExceptions()[1]);
            $this->assertSame('releases.0.date', $exception->getExceptions()[1]->getPathAsString());
        }
    }

    private function getMapper(): RestApiBundle\Services\Mapper\Mapper
    {
        return $this->getContainer()->get(RestApiBundle\Services\Mapper\Mapper::class);
    }
}
