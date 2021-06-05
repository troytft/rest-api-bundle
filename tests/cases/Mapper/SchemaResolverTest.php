<?php

class SchemaResolverTest extends Tests\BaseTestCase
{
    public function testGetSchemaByClassName()
    {
        $properties = $this
            ->getSchemaResolver()
            ->resolve(Tests\Fixture\Mapper\Movie::class)
            ->getProperties();

        $this->assertCount(6, $properties);

        $this->assertArrayHasKey('name', $properties);
        $this->assertTrue($properties['name']->isScalar());

        $this->assertArrayHasKey('rating', $properties);
        $this->assertTrue($properties['rating']->isScalar());

        $this->assertArrayHasKey('lengthMinutes', $properties);
        $this->assertTrue($properties['lengthMinutes']->isScalar());

        $this->assertArrayHasKey('isOnlineWatchAvailable', $properties);
        $this->assertTrue($properties['isOnlineWatchAvailable']->isScalar());

        $this->assertArrayHasKey('genres', $properties);
        $this->assertTrue($properties['genres']->isCollection());

        $this->assertArrayHasKey('releases', $properties);
        $this->assertTrue($properties['releases']->isCollection());
    }

    private function getSchemaResolver(): RestApiBundle\Services\Mapper\SchemaResolver
    {
        return $this->getContainer()->get(RestApiBundle\Services\Mapper\SchemaResolver::class);
    }
}
