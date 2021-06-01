<?php

class ResponseModelResolverTest extends Tests\BaseTestCase
{
    public function testSchemaFromTypeHints()
    {
        $reference = $this->getResponseModelResolver()->resolveReferenceByClass(TestApp\ResponseModel\ModelWithTypeHint::class);
        $this->assertJsonStringEqualsJsonString('{"$ref":"#\/components\/schemas\/ModelWithTypeHint"}', json_encode($reference->getSerializableData()));

        $schemas = $this->getResponseModelResolver()->dumpSchemas();

        $this->assertCount(2, $schemas);
        $this->assertArrayHasKey('ModelWithTypeHint', $schemas);
        $this->assertArrayHasKey('CombinedModel', $schemas);

        $this->assertMatchesJsonSnapshot(json_encode($schemas['ModelWithTypeHint']->getSerializableData()));
        $this->assertMatchesJsonSnapshot(json_encode($schemas['CombinedModel']->getSerializableData()));
    }

    public function testSchemaFromDocBlocks()
    {
        $reference = $this->getResponseModelResolver()->resolveReferenceByClass(TestApp\ResponseModel\ModelWithDocBlock::class);
        $this->assertJsonStringEqualsJsonString('{"$ref":"#\/components\/schemas\/ModelWithDocBlock"}', json_encode($reference->getSerializableData()));

        $schemas = $this->getResponseModelResolver()->dumpSchemas();

        $this->assertCount(2, $schemas);
        $this->assertArrayHasKey('ModelWithDocBlock', $schemas);
        $this->assertArrayHasKey('CombinedModel', $schemas);

        $this->assertMatchesJsonSnapshot(json_encode($schemas['ModelWithDocBlock']->getSerializableData()));
        $this->assertMatchesJsonSnapshot(json_encode($schemas['CombinedModel']->getSerializableData()));
    }

    protected function getResponseModelResolver(): RestApiBundle\Services\OpenApi\ResponseModelResolver
    {
        /** @var RestApiBundle\Services\OpenApi\ResponseModelResolver $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\OpenApi\ResponseModelResolver::class);

        return $result;
    }
}
