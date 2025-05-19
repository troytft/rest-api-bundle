<?php

namespace cases\ResponseModel;

use RestApiBundle;
use Tests;

class ResponseModelResolverTest extends Tests\BaseTestCase
{
    public function testDate(): void
    {
        $this->getResponseModelResolver()->resolveReference(Tests\Fixture\ResponseModel\DateType::class);
        $schemas = $this->getResponseModelResolver()->dumpSchemas();

        $this->assertMatchesOpenApiSchemaSnapshot($schemas[array_key_first($schemas)]);
    }

    public function testDateTime(): void
    {
        $this->getResponseModelResolver()->resolveReference(Tests\Fixture\ResponseModel\DateTimeType::class);
        $schemas = $this->getResponseModelResolver()->dumpSchemas();

        $this->assertMatchesOpenApiSchemaSnapshot($schemas[array_key_first($schemas)]);
    }

    public function testPolyfillEnum(): void
    {
        $this->getResponseModelResolver()->resolveReference(Tests\Fixture\ResponseModel\PolyfillEnumType::class);
        $schemas = $this->getResponseModelResolver()->dumpSchemas();

        $this->assertMatchesOpenApiSchemaSnapshot($schemas[array_key_first($schemas)]);
    }

    public function testPhpEnum(): void
    {
        $this->getResponseModelResolver()->resolveReference(Tests\Fixture\ResponseModel\PhpEnumType::class);
        $schemas = $this->getResponseModelResolver()->dumpSchemas();

        $this->assertMatchesOpenApiSchemaSnapshot($schemas[array_key_first($schemas)]);
    }

    private function getResponseModelResolver(): RestApiBundle\Services\OpenApi\ResponseModelResolver
    {
        return $this->getContainer()->get(RestApiBundle\Services\OpenApi\ResponseModelResolver::class);
    }
}
