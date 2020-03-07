<?php

namespace Tests\TestCase\Services\Docs;

use RestApiBundle;
use Tests;
use cebe\openapi\spec as OpenApi;

class OpenApiSpecificationGeneratorTest extends Tests\TestCase\BaseTestCase
{
    public function testConvertBooleanType()
    {
        $schema = new RestApiBundle\DTO\Docs\Schema\BooleanType(false);
        $openApiSchema = $this->invokePrivateMethod($this->getOpenApiSpecificationGenerator(), 'convertBooleanType', [$schema]);

        $this->assertInstanceOf(OpenApi\Schema::class, $openApiSchema);
        $this->assertSame(['type' => 'boolean', 'nullable' => false], (array) $openApiSchema->getSerializableData());
    }
}
