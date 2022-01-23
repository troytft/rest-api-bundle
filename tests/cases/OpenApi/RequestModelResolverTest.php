<?php

class RequestModelResolverTest extends Tests\BaseTestCase
{
    public function testEnumSchema(): void
    {
        $schema = $this->getRequestModelResolver()->resolve(Tests\Fixture\OpenApi\RequestModelResolverTest\TestEnumSchemaModel::class);

        $this->assertMatchesJsonSnapshot($this->convertOpenApiToJson($schema));
    }

    private function getRequestModelResolver(): RestApiBundle\Services\OpenApi\RequestModelResolver
    {
        return $this->getContainer()->get(RestApiBundle\Services\OpenApi\RequestModelResolver::class);
    }
}
