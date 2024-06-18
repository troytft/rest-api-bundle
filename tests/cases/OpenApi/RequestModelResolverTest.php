<?php

class RequestModelResolverTest extends Tests\BaseTestCase
{
    public function testEnum(): void
    {
        $schema = $this->getRequestModelResolver()->resolve(Tests\Fixture\OpenApi\RequestModelResolverTest\TestEnumModel::class);

        $this->assertMatchesOpenApiSchemaSnapshot($schema);
    }

    public function testUploadedFile(): void
    {
        $schema = $this->getRequestModelResolver()->resolve(Tests\Fixture\OpenApi\RequestModelResolverTest\TestUploadedFileModel::class);

        $this->assertMatchesOpenApiSchemaSnapshot($schema);
    }

    private function getRequestModelResolver(): RestApiBundle\Services\OpenApi\RequestModelResolver
    {
        return $this->getContainer()->get(RestApiBundle\Services\OpenApi\RequestModelResolver::class);
    }
}
