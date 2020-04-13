<?php

namespace Tests\TestCase\Services\Docs;

use RestApiBundle;
use Tests;
use cebe\openapi\spec as OpenApi;
use function var_dump;
use function var_export;

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

    public function testConvertRequestModelToParameters()
    {
        $objectProperties = [
            'offset' => new RestApiBundle\DTO\Docs\Schema\IntegerType(false),
            'limit' => new RestApiBundle\DTO\Docs\Schema\IntegerType(false),
        ];
        $objectType = new RestApiBundle\DTO\Docs\Schema\ObjectType($objectProperties, false);

        /** @var OpenApi\Parameter[] $openApiParameters */
        $openApiParameters = $this->invokePrivateMethod($this->getOpenApiSpecificationGenerator(), 'convertRequestModelToParameters', [$objectType]);

        $this->assertIsArray($openApiParameters);
        $this->assertCount(2, $openApiParameters);

        $this->assertInstanceOf(OpenApi\Parameter::class, $openApiParameters[0]);
        $this->assertSame([
            'name' => 'offset',
            'in' => 'query',
            'required' => true,
            'schema' => [
                'type' => 'integer',
                'nullable' => false,
            ],
        ], $this->convertStdClassToArray($openApiParameters[0]->getSerializableData()));

        $this->assertInstanceOf(OpenApi\Parameter::class, $openApiParameters[1]);
        $this->assertSame([
            'name' => 'limit',
            'in' => 'query',
            'required' => true,
            'schema' => [
                'type' => 'integer',
                'nullable' => false,
            ],
        ], $this->convertStdClassToArray($openApiParameters[1]->getSerializableData()));
    }
}
