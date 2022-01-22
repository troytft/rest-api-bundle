<?php

class RequestModelResolverTest extends Tests\BaseTestCase
{
    public function testEnumTransformerSchema(): void
    {
        $schema = $this->getRequestModelResolver()->resolve(\Tests\Fixture\EnumTransformerTest\Model::class);
        $this->assertMatchesJsonSnapshot(\RestApiBundle\Helper\OpenApiHelper::schemaToJson($schema));
    }

    private function getRequestModelResolver(): RestApiBundle\Services\OpenApi\Schema\RequestModelResolver
    {
        return $this->getContainer()->get(RestApiBundle\Services\OpenApi\Schema\RequestModelResolver::class);
    }
}
