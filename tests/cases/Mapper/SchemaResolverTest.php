<?php

class SchemaResolverTest extends Tests\BaseTestCase
{
    public function testTypesByTypeHint()
    {
        $schema = $this->getSchemaResolver()->resolve(Tests\Fixture\Mapper\SchemaResolver\TypesByTypeHint::class);
        $this->assertMatchesJsonSnapshot(json_encode($schema));
    }

    public function testTypesByDocBlock()
    {
        $schema = $this->getSchemaResolver()->resolve(Tests\Fixture\Mapper\SchemaResolver\TypesByDocBlock::class);
        $this->assertMatchesJsonSnapshot(json_encode($schema));
    }

    public function testExposeAll()
    {
        $schema = $this->getSchemaResolver()->resolve(Tests\Fixture\Mapper\SchemaResolver\ExposeAll::class);
        $this->assertMatchesJsonSnapshot(json_encode($schema));
    }

    private function getSchemaResolver(): RestApiBundle\Services\Mapper\SchemaResolver
    {
        return $this->getContainer()->get(RestApiBundle\Services\Mapper\SchemaResolver::class);
    }
}
