<?php

namespace Tests\TestCase\Services\Docs;

use RestApiBundle;
use Tests;
use cebe\openapi\spec as OpenApi;

class OpenApiSpecificationGeneratorTest extends Tests\TestCase\BaseTestCase
{
    public function testConvertPathParameter()
    {
        $pathParameter = new RestApiBundle\DTO\Docs\PathParameter('parameterName', new RestApiBundle\DTO\Docs\Schema\StringType(false));
        $openApiParameter = $this->invokePrivateMethod($this->getOpenApiSpecificationGenerator(), 'convertPathParameter', [$pathParameter]);

        $this->assertInstanceOf(OpenApi\Parameter::class, $openApiParameter);

        $expected = [
            'name' => 'parameterName',
            'in' => 'path',
            'required' => true,
            'schema' => [
                'type' => 'string',
                'nullable' => false,
            ],
        ];

        $this->assertSame($expected, $this->convertStdClassToArray($openApiParameter->getSerializableData()));
    }

    public function testConvertSchemaType()
    {
        $booleanType = new RestApiBundle\DTO\Docs\Schema\BooleanType(false);
        $openApiSchema = $this->invokePrivateMethod($this->getOpenApiSpecificationGenerator(), 'convertSchemaType', [$booleanType]);

        $this->assertInstanceOf(OpenApi\Schema::class, $openApiSchema);

        $expected = [
            'type' => 'boolean',
            'nullable' => false,
        ];

        $this->assertSame($expected, $this->convertStdClassToArray($openApiSchema->getSerializableData()));

        // nullable
        $booleanType = new RestApiBundle\DTO\Docs\Schema\BooleanType(true);
        $openApiSchema = $this->invokePrivateMethod($this->getOpenApiSpecificationGenerator(), 'convertSchemaType', [$booleanType]);

        $this->assertInstanceOf(OpenApi\Schema::class, $openApiSchema);

        $expected = [
            'type' => 'boolean',
            'nullable' => true,
        ];

        $this->assertSame($expected, $this->convertStdClassToArray($openApiSchema->getSerializableData()));
    }
}
