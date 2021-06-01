<?php

class ErrorPathTest extends Tests\BaseTestCase
{
    public function testObjectProperty()
    {
        $model = new Tests\Fixture\Mapper\Movie();
        $data = [
            'name' => '',
            'genres' => null,
            'releases' => [],
        ];

        try {
            $this->getMapper()->map($model, $data);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\StackedMappingException $exception) {
            $this->assertCount(4, $exception->getExceptions());

            $this->assertInstanceOf(RestApiBundle\Exception\Mapper\MappingValidation\CanNotBeNullException::class, $exception->getExceptions()[0]);
            $this->assertSame('genres', $exception->getExceptions()[0]->getPathAsString());

            $this->assertInstanceOf(RestApiBundle\Exception\Mapper\MappingValidation\CanNotBeNullException::class, $exception->getExceptions()[1]);
            $this->assertSame('rating', $exception->getExceptions()[1]->getPathAsString());

            $this->assertInstanceOf(RestApiBundle\Exception\Mapper\MappingValidation\CanNotBeNullException::class, $exception->getExceptions()[2]);
            $this->assertSame('lengthMinutes', $exception->getExceptions()[2]->getPathAsString());

            $this->assertInstanceOf(RestApiBundle\Exception\Mapper\MappingValidation\CanNotBeNullException::class, $exception->getExceptions()[3]);
            $this->assertSame('isOnlineWatchAvailable', $exception->getExceptions()[3]->getPathAsString());
        }
    }

    public function testCollectionItem()
    {
        $model = new Tests\Fixture\Mapper\Movie();
        $data = [
            'name' => '',
            'genres' => [],
            'releases' => [
                null,
            ]
        ];

        try {
            $this->getMapper()->map($model, $data);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\StackedMappingException $exception) {
            $this->assertCount(4, $exception->getExceptions());

            $this->assertInstanceOf(RestApiBundle\Exception\Mapper\MappingValidation\CanNotBeNullException::class, $exception->getExceptions()[0]);
            $this->assertSame('releases.0', $exception->getExceptions()[0]->getPathAsString());

            $this->assertInstanceOf(RestApiBundle\Exception\Mapper\MappingValidation\CanNotBeNullException::class, $exception->getExceptions()[1]);
            $this->assertSame('rating', $exception->getExceptions()[1]->getPathAsString());

            $this->assertInstanceOf(RestApiBundle\Exception\Mapper\MappingValidation\CanNotBeNullException::class, $exception->getExceptions()[2]);
            $this->assertSame('lengthMinutes', $exception->getExceptions()[2]->getPathAsString());

            $this->assertInstanceOf(RestApiBundle\Exception\Mapper\MappingValidation\CanNotBeNullException::class, $exception->getExceptions()[3]);
            $this->assertSame('isOnlineWatchAvailable', $exception->getExceptions()[3]->getPathAsString());
        }
    }

    public function testObjectPropertyInsideCollection()
    {
        // object property inside collection
        $model = new Tests\Fixture\Mapper\Movie();
        $data = [
            'name' => '',
            'genres' => [],
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
            $this->assertCount(5, $exception->getExceptions());

            $this->assertInstanceOf(RestApiBundle\Exception\Mapper\MappingValidation\CanNotBeNullException::class, $exception->getExceptions()[0]);
            $this->assertSame('releases.0.country', $exception->getExceptions()[0]->getPathAsString());

            $this->assertInstanceOf(RestApiBundle\Exception\Mapper\MappingValidation\CanNotBeNullException::class, $exception->getExceptions()[1]);
            $this->assertSame('releases.0.date', $exception->getExceptions()[1]->getPathAsString());

            $this->assertInstanceOf(RestApiBundle\Exception\Mapper\MappingValidation\CanNotBeNullException::class, $exception->getExceptions()[2]);
            $this->assertSame('rating', $exception->getExceptions()[2]->getPathAsString());

            $this->assertInstanceOf(RestApiBundle\Exception\Mapper\MappingValidation\CanNotBeNullException::class, $exception->getExceptions()[3]);
            $this->assertSame('lengthMinutes', $exception->getExceptions()[3]->getPathAsString());

            $this->assertInstanceOf(RestApiBundle\Exception\Mapper\MappingValidation\CanNotBeNullException::class, $exception->getExceptions()[4]);
            $this->assertSame('isOnlineWatchAvailable', $exception->getExceptions()[4]->getPathAsString());
        }
    }

    private function getMapper(): RestApiBundle\Services\Mapper\Mapper
    {
        return $this->getContainer()->get(RestApiBundle\Services\Mapper\Mapper::class);
    }
}
