<?php

class RequestModelResolver extends Tests\BaseTestCase
{
    public function testEnumSchema(): void
    {
        $schema = $this->getRequestModelResolver()->resolve(\Tests\Fixture\OpenApi\RequestModelResolverTest\Enum::class);
        $this->assertMatchesJsonSnapshot(\RestApiBundle\Helper\OpenApiHelper::schemaToJson($schema));
    }

    private function getRequestModelResolver(): RestApiBundle\Services\OpenApi\Schema\RequestModelResolver
    {
        return $this->getContainer()->get(RestApiBundle\Services\OpenApi\Schema\RequestModelResolver::class);
    }
}
