<?php

class ClassImportTest extends Tests\BaseTestCase
{
    public function testFullyQualifiedNameImport(): void
    {
        $schema = $this->getRequestModelResolver()->resolve(\Tests\Fixture\ClassImportTest\RequestModel\FullyQualifiedNameImport::class);

        $this->assertMatchesOpenApiSchemaSnapshot($schema);
    }

    public function testRelativeImport(): void
    {
        $schema = $this->getRequestModelResolver()->resolve(\Tests\Fixture\ClassImportTest\RequestModel\RelativeImport::class);

        $this->assertMatchesOpenApiSchemaSnapshot($schema);
    }

    public function testUseAlias(): void
    {
        $schema = $this->getRequestModelResolver()->resolve(\Tests\Fixture\ClassImportTest\RequestModel\UseAlias::class);

        $this->assertMatchesOpenApiSchemaSnapshot($schema);
    }

    private function getRequestModelResolver(): RestApiBundle\Services\OpenApi\RequestModelResolver
    {
        return $this->getContainer()->get(RestApiBundle\Services\OpenApi\RequestModelResolver::class);
    }
}
