<?php

class SchemaResolverTest extends Tests\BaseTestCase
{
    public function testGetSchemaByClassName()
    {
        $properties = $this
            ->getSchemaResolver()
            ->resolveByClass(Tests\Fixture\Mapper\Movie::class)
            ->getProperties();

        $this->assertCount(6, $properties);

        $this->assertArrayHasKey('name', $properties);
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\ScalarType::class, $properties['name']);

        $this->assertArrayHasKey('rating', $properties);
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\ScalarType::class, $properties['rating']);

        $this->assertArrayHasKey('lengthMinutes', $properties);
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\ScalarType::class, $properties['lengthMinutes']);

        $this->assertArrayHasKey('isOnlineWatchAvailable', $properties);
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\ScalarType::class, $properties['isOnlineWatchAvailable']);

        $this->assertArrayHasKey('genres', $properties);
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\CollectionType::class, $properties['genres']);

        $this->assertArrayHasKey('releases', $properties);
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\CollectionType::class, $properties['releases']);
    }

    public function testGetSchemaByClassInstance()
    {
        $properties = $this
            ->getSchemaResolver()
            ->resolveByInstance(new Tests\Fixture\Mapper\Movie())
            ->getProperties();

        $this->assertCount(6, $properties);

        $this->assertArrayHasKey('name', $properties);
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\ScalarType::class, $properties['name']);

        $this->assertArrayHasKey('rating', $properties);
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\ScalarType::class, $properties['rating']);

        $this->assertArrayHasKey('lengthMinutes', $properties);
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\ScalarType::class, $properties['lengthMinutes']);

        $this->assertArrayHasKey('isOnlineWatchAvailable', $properties);
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\ScalarType::class, $properties['isOnlineWatchAvailable']);

        $this->assertArrayHasKey('genres', $properties);
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\CollectionType::class, $properties['genres']);

        $this->assertArrayHasKey('releases', $properties);
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema\CollectionType::class, $properties['releases']);
    }

    private function getSchemaResolver(): RestApiBundle\Services\Mapper\SchemaResolver
    {
        return $this->getContainer()->get(RestApiBundle\Services\Mapper\SchemaResolver::class);
    }
}
