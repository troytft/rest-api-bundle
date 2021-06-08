<?php

use RestApiBundle\Model\Mapper\Schema;

class SchemaResolverTest extends Tests\BaseTestCase
{
    public function testGetSchemaByClassName()
    {
        $schema = $this->getSchemaResolver()->resolve(Tests\Fixture\Mapper\Movie::class);

        $this->assertCount(6, $schema->properties);

        $name = $schema->properties['name'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $name);
        $this->assertEquals(Schema::TRANSFORMER_AWARE_TYPE, $name->type);

        $rating = $schema->properties['rating'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $rating);
        $this->assertEquals(Schema::TRANSFORMER_AWARE_TYPE, $rating->type);

        $lengthMinutes = $schema->properties['lengthMinutes'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $lengthMinutes);
        $this->assertEquals(Schema::TRANSFORMER_AWARE_TYPE, $lengthMinutes->type);

        $isOnlineWatchAvailable = $schema->properties['isOnlineWatchAvailable'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $isOnlineWatchAvailable);
        $this->assertEquals(Schema::TRANSFORMER_AWARE_TYPE, $isOnlineWatchAvailable->type);

        $genres = $schema->properties['genres'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $genres);
        $this->assertEquals(Schema::ARRAY_TYPE, $genres->type);

        $releases = $schema->properties['releases'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $releases);
        $this->assertEquals(Schema::ARRAY_TYPE, $releases->type);
    }

    public function testAutoConvertToEntitiesCollection()
    {
        $schema = $this->getSchemaResolver()->resolve(Tests\Fixture\Mapper\AutoConvertToEntitiesCollection\Model::class);

        $this->assertCount(1, $schema->properties);

        $books = $schema->properties['books'] ?? null;
        $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $books);
        $this->assertEquals(RestApiBundle\Services\Mapper\Transformer\EntitiesCollectionTransformer::class, $books->transformerClass);
    }

    public function testAttributesAndAnnotationsTogether()
    {
        $schema = $this->getSchemaResolver()->resolve(Tests\Fixture\Mapper\Php8Attribute\Model::class);

        if (\PHP_VERSION_ID >= 80000) {
            $this->assertCount(3, $schema->properties);

            $name = $schema->properties['name'] ?? null;
            $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $name);
            $this->assertEquals(Schema::TRANSFORMER_AWARE_TYPE, $name->type);

            $date = $schema->properties['date'] ?? null;
            $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $date);
            $this->assertEquals(Schema::TRANSFORMER_AWARE_TYPE, $date->type);

            $rating = $schema->properties['rating'] ?? null;
            $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $rating);
            $this->assertEquals(Schema::TRANSFORMER_AWARE_TYPE, $rating->type);
        } else {
            $this->assertCount(1, $schema->properties);
            
            $rating = $schema->properties['rating'] ?? null;
            $this->assertInstanceOf(RestApiBundle\Model\Mapper\Schema::class, $rating);
            $this->assertEquals(Schema::TRANSFORMER_AWARE_TYPE, $rating->type);
        }
    }

    private function getSchemaResolver(): RestApiBundle\Services\Mapper\SchemaResolver
    {
        return $this->getContainer()->get(RestApiBundle\Services\Mapper\SchemaResolver::class);
    }
}
