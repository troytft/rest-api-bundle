<?php

class MapperTest extends Tests\BaseTestCase
{
    public function testClearMissingEnabled()
    {
        $context = new RestApiBundle\Model\Mapper\Context();
        $context
            ->isClearMissing = true;

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
    }

    public function testClearMissingDisabled()
    {
        $context = new RestApiBundle\Model\Mapper\Context();
        $context
            ->isClearMissing = false;

        $movie = new Tests\Fixture\Mapper\Movie();
        $this->assertSame('Taxi 2', $movie->name);

        $this->getMapper()->map($movie, [], $context);
        $this->assertSame('Taxi 2', $movie->name);
    }

    public function testUndefinedKey()
    {
        $context = new RestApiBundle\Model\Mapper\Context();
        $context
            ->isClearMissing = false;

        $model = new Tests\Fixture\Mapper\Movie();
        $data = [
            'releases' => [
                [
                    'name' => 'Release 1',
                ]
            ]
        ];

        try {
            $this->getMapper()->map($model, $data, $context);
            $this->fail();
        } catch (RestApiBundle\Exception\Mapper\StackedMappingException $exception) {
            $this->assertCount(1, $exception->getExceptions());
            $this->assertInstanceOf(RestApiBundle\Exception\Mapper\MappingValidation\UndefinedKeyException::class, $exception->getExceptions()[0]);
            $this->assertSame('releases.0.name', $exception->getExceptions()[0]->getPathAsString());
        }
    }

    private function getMapper(): RestApiBundle\Services\Mapper\Mapper
    {
        return $this->getContainer()->get(RestApiBundle\Services\Mapper\Mapper::class);
    }
}
