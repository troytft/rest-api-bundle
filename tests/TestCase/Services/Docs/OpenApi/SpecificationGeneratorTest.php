<?php

namespace Tests\TestCase\Services\Docs\OpenApi;

use cebe\openapi\SpecObjectInterface;
use Symfony;
use RestApiBundle;
use Tests;
use cebe\openapi\spec as OpenApi;
use function json_decode;
use function json_encode;

class SpecificationGeneratorTest extends Tests\TestCase\BaseTestCase
{
    public function testCreateParameter()
    {
        $schema = new RestApiBundle\DTO\Docs\Schema\StringType(false);
        $schema
            ->setDescription('Description');

        /** @var OpenApi\Parameter $openApiParameter */
        $openApiParameter = $this->invokePrivateMethod($this->getOpenApiSpecificationGenerator(), 'createParameter', ['path', 'parameterName', $schema]);

        $this->assertInstanceOf(OpenApi\Parameter::class, $openApiParameter);

        $expected = [
            'name' => 'parameterName',
            'in' => 'path',
            'description' => 'Description',
            'required' => true,
            'schema' => [
                'type' => 'string',
                'nullable' => false,
            ],
        ];

        $this->assertSame($expected, $this->convertOpenApiToArray($openApiParameter));
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

        $this->assertSame($expected, $this->convertOpenApiToArray($openApiSchema));

        // nullable
        $booleanType = new RestApiBundle\DTO\Docs\Schema\BooleanType(true);
        $openApiSchema = $this->invokePrivateMethod($this->getOpenApiSpecificationGenerator(), 'convertSchemaType', [$booleanType]);

        $this->assertInstanceOf(OpenApi\Schema::class, $openApiSchema);

        $expected = [
            'type' => 'boolean',
            'nullable' => true,
        ];

        $this->assertSame($expected, $this->convertOpenApiToArray($openApiSchema));
    }

    public function testRangeConstraint()
    {
        $constraint = new Symfony\Component\Validator\Constraints\Range([
            'min' => 0,
            'max' => \PHP_INT_MAX,
        ]);

        $integerType = new RestApiBundle\DTO\Docs\Schema\IntegerType(false);
        $integerType
            ->setConstraints([$constraint]);

        $openApiSchema = $this->invokePrivateMethod($this->getOpenApiSpecificationGenerator(), 'convertSchemaType', [$integerType]);

        $this->assertInstanceOf(OpenApi\Schema::class, $openApiSchema);

        $expected = [
            'type' => 'integer',
            'nullable' => false,
            'minimum' => 0,
            'maximum' => 9223372036854775807,
        ];

        $this->assertSame($expected, $this->convertOpenApiToArray($openApiSchema));
    }

    public function testChoiceConstraintWithChoices()
    {
        $constraint = new Symfony\Component\Validator\Constraints\Choice([
            'choices' => Tests\TestApp\TestBundle\Enum\Status::getValues(),
        ]);

        $stringType = new RestApiBundle\DTO\Docs\Schema\StringType(false);
        $stringType
            ->setConstraints([$constraint]);

        $openApiSchema = $this->invokePrivateMethod($this->getOpenApiSpecificationGenerator(), 'convertSchemaType', [$stringType]);

        $this->assertInstanceOf(OpenApi\Schema::class, $openApiSchema);

        $expected = [
            'type' => 'string',
            'nullable' => false,
            'enum' => [
                'created',
                'published',
                'archived',
            ],
        ];
        $this->assertSame($expected, $this->convertOpenApiToArray($openApiSchema));
    }

    public function testChoiceConstraintWithCallback()
    {
        $constraint = new Symfony\Component\Validator\Constraints\Choice([
            'callback' => 'Tests\TestApp\TestBundle\Enum\Status::getValues',
        ]);

        $stringType = new RestApiBundle\DTO\Docs\Schema\StringType(false);
        $stringType
            ->setConstraints([$constraint]);

        $openApiSchema = $this->invokePrivateMethod($this->getOpenApiSpecificationGenerator(), 'convertSchemaType', [$stringType]);

        $this->assertInstanceOf(OpenApi\Schema::class, $openApiSchema);

        $expected = [
            'type' => 'string',
            'nullable' => false,
            'enum' => [
                'created',
                'published',
                'archived',
            ],
        ];
        $this->assertSame($expected, $this->convertOpenApiToArray($openApiSchema));
    }

    public function testCountConstraint()
    {
        $constraint = new Symfony\Component\Validator\Constraints\Count([
            'min' => 1,
            'max' => 12,
        ]);

        $arrayType = new RestApiBundle\DTO\Docs\Schema\ArrayType(new RestApiBundle\DTO\Docs\Schema\StringType(false), false);
        $arrayType
            ->setConstraints([$constraint]);

        $openApiSchema = $this->invokePrivateMethod($this->getOpenApiSpecificationGenerator(), 'convertSchemaType', [$arrayType]);

        $this->assertInstanceOf(OpenApi\Schema::class, $openApiSchema);

        $expected = [
            'type' => 'array',
            'items' => [
                'type' => 'string',
                'nullable' => false,
            ],
            'nullable' => false,
            'minItems' => 1,
            'maxItems' => 12,
        ];
        $this->assertSame($expected, $this->convertOpenApiToArray($openApiSchema));
    }

    public function testLengthConstraint()
    {
        $constraint = new Symfony\Component\Validator\Constraints\Length([
            'min' => 1,
            'max' => 12,
        ]);

        $stringType = new RestApiBundle\DTO\Docs\Schema\StringType(false);
        $stringType
            ->setConstraints([$constraint]);

        $openApiSchema = $this->invokePrivateMethod($this->getOpenApiSpecificationGenerator(), 'convertSchemaType', [$stringType]);

        $this->assertInstanceOf(OpenApi\Schema::class, $openApiSchema);

        $expected = [
            'type' => 'string',
            'nullable' => false,
            'minLength' => 1,
            'maxLength' => 12,
        ];
        $this->assertSame($expected, $this->convertOpenApiToArray($openApiSchema));
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
        ], $this->convertOpenApiToArray($openApiParameters[0]));

        $this->assertInstanceOf(OpenApi\Parameter::class, $openApiParameters[1]);
        $this->assertSame([
            'name' => 'limit',
            'in' => 'query',
            'required' => true,
            'schema' => [
                'type' => 'integer',
                'nullable' => false,
            ],
        ], $this->convertOpenApiToArray($openApiParameters[1]));
    }

    public function testConvertRequestModelToRequestBody()
    {
        $objectProperties = [
            'offset' => new RestApiBundle\DTO\Docs\Schema\IntegerType(false),
            'limit' => new RestApiBundle\DTO\Docs\Schema\IntegerType(false),
        ];
        $objectType = new RestApiBundle\DTO\Docs\Schema\ObjectType($objectProperties, false);

        /** @var OpenApi\RequestBody $requestBody */
        $requestBody = $this->invokePrivateMethod($this->getOpenApiSpecificationGenerator(), 'convertRequestModelToRequestBody', [$objectType]);

        $expected = [
            'description' => 'Request body',
            'content' => [
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'offset' => [
                                'type' => 'integer',
                                'nullable' => false,
                            ],
                            'limit' => [
                                'type' => 'integer',
                                'nullable' => false,
                            ],
                        ],
                        'nullable' => false,
                    ],
                ],
            ],
            'required' => true,
        ];

        $this->assertSame($expected, $this->convertOpenApiToArray($requestBody));
    }

    public function testConvertDateTimeType()
    {
        $dateTimeType = new RestApiBundle\DTO\Docs\Schema\DateTimeType(false);
        $openApiSchema = $this->invokePrivateMethod($this->getOpenApiSpecificationGenerator(), 'convertSchemaType', [$dateTimeType]);

        $this->assertInstanceOf(OpenApi\Schema::class, $openApiSchema);

        $expected = [
            'type' => 'string',
            'format' => 'date-time',
            'nullable' => false,
        ];

        $this->assertSame($expected, $this->convertOpenApiToArray($openApiSchema));
    }

    public function testConvertDateType()
    {
        $dateType = new RestApiBundle\DTO\Docs\Schema\DateType(false);
        $openApiSchema = $this->invokePrivateMethod($this->getOpenApiSpecificationGenerator(), 'convertSchemaType', [$dateType]);

        $this->assertInstanceOf(OpenApi\Schema::class, $openApiSchema);

        $expected = [
            'type' => 'string',
            'format' => 'date',
            'nullable' => false,
        ];

        $this->assertSame($expected, $this->convertOpenApiToArray($openApiSchema));
    }

    protected function convertStdClassToArray(\stdClass $stdClass): array
    {
        return json_decode(json_encode($stdClass), true);
    }
    
    private function convertOpenApiToArray(SpecObjectInterface $specObject): array
    {
        return $this->convertStdClassToArray($specObject->getSerializableData());
    }
}
