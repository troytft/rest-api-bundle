<?php

class ResponseModelResolverTest extends Tests\BaseTestCase
{
    public function testDate(): void
    {
        $this->getResponseModelResolver()->resolveReference(Tests\Fixture\OpenApi\ResponseModelResolverTest\ResponseModel\TestDateModel::class);
        $schemas = $this->getResponseModelResolver()->dumpSchemas();

        $this->assertMatchesOpenApiSchemaSnapshot($schemas[array_key_first($schemas)]);
    }

    private function getResponseModelResolver(): RestApiBundle\Services\OpenApi\ResponseModelResolver
    {
        return $this->getContainer()->get(RestApiBundle\Services\OpenApi\ResponseModelResolver::class);
    }
}
