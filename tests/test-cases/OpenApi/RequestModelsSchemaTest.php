<?php

class RequestModelsSchemaTest extends Tests\BaseTestCase
{
    public function testEnumSchema(): void
    {
        $schema = $this->getRequestModelResolver()->resolve(\Tests\Fixture\TestCases\OpenApi\RequestModelsSchemaTest\ModelWithEnums::class);

        $this->assertMatchesJsonSnapshot(\RestApiBundle\Helper\OpenApiHelper::toJson($schema));
    }

    private function getRequestModelResolver(): RestApiBundle\Services\OpenApi\Schema\RequestModelResolver
    {
        return $this->getContainer()->get(RestApiBundle\Services\OpenApi\Schema\RequestModelResolver::class);
    }
}
