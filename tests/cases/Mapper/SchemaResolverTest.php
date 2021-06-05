<?php

class SchemaResolverTest extends Tests\BaseTestCase
{
    public function testGetSchemaByClassName()
    {
        $schema = $this->getSchemaResolver()->resolve(Tests\Fixture\Mapper\Movie::class);

        $this->assertCount(6, $schema->getProperties());

        $name = $schema->getProperties()['name'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $name);
        $this->assertTrue($name->isTransformerAwareType());

        $rating = $schema->getProperties()['rating'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $rating);
        $this->assertTrue($rating->isTransformerAwareType());

        $lengthMinutes = $schema->getProperties()['lengthMinutes'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $lengthMinutes);
        $this->assertTrue($lengthMinutes->isTransformerAwareType());

        $isOnlineWatchAvailable = $schema->getProperties()['isOnlineWatchAvailable'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $isOnlineWatchAvailable);
        $this->assertTrue($isOnlineWatchAvailable->isTransformerAwareType());

        $genres = $schema->getProperties()['genres'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $genres);
        $this->assertTrue($genres->isArrayType());

        $releases = $schema->getProperties()['releases'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $releases);
        $this->assertTrue($releases->isArrayType());
    }

    private function getSchemaResolver(): RestApiBundle\Services\Mapper\SchemaResolver
    {
        return $this->getContainer()->get(RestApiBundle\Services\Mapper\SchemaResolver::class);
    }
}
