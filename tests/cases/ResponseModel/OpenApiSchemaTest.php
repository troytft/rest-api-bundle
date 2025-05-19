<?php

namespace cases\ResponseModel;

use RestApiBundle;
use Tests;

class OpenApiSchemaTest extends Tests\BaseTestCase
{
    public function testDateType(): void
    {
        $this->getResponseModelResolver()->resolveReference(Tests\Fixture\ResponseModel\DateType::class);
        $schemas = $this->getResponseModelResolver()->dumpSchemas();

        $this->assertMatchesOpenApiSchemaSnapshot($schemas[array_key_first($schemas)]);
    }

    public function testDateTimeType(): void
    {
        $this->getResponseModelResolver()->resolveReference(Tests\Fixture\ResponseModel\DateTimeType::class);
        $schemas = $this->getResponseModelResolver()->dumpSchemas();

        $this->assertMatchesOpenApiSchemaSnapshot($schemas[array_key_first($schemas)]);
    }

    public function testPolyfillEnumType(): void
    {
        $this->getResponseModelResolver()->resolveReference(Tests\Fixture\ResponseModel\PolyfillEnumType::class);
        $schemas = $this->getResponseModelResolver()->dumpSchemas();

        $this->assertMatchesOpenApiSchemaSnapshot($schemas[array_key_first($schemas)]);
    }

    public function testPhpEnumType(): void
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
