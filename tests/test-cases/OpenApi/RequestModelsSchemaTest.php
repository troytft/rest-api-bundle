<?php

class RequestModelsSchemaTest extends Tests\BaseTestCase
{
    public function testEnumSchema(): void
    {
        $schema = $this->getRequestModelResolver()->resolve(Tests\Fixture\OpenApi\RequestModelsSchemaTest\TestEnumSchemaModel::class);

        $this->assertMatchesJsonSnapshot($this->convertOpenApiToJson($schema));
    }

    private function getRequestModelResolver(): RestApiBundle\Services\OpenApi\Schema\RequestModelResolver
    {
        return $this->getContainer()->get(RestApiBundle\Services\OpenApi\Schema\RequestModelResolver::class);
    }
}
