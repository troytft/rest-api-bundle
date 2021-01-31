<?php

use cebe\openapi\SpecObjectInterface;
use cebe\openapi\spec as OpenApi;

class SpecificationGeneratorTest extends Tests\BaseTestCase
{
    public function testCreateParameter()
    {
        $schema = new RestApiBundle\DTO\Docs\Types\StringType(false);
        $schema
            ->setDescription('Description');

        /** @var OpenApi\Parameter $openApiParameter */
        $openApiParameter = $this->invokePrivateMethod($this->getSpecificationGenerator(), 'createParameter', ['path', 'parameterName', $schema]);

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
        $booleanType = new RestApiBundle\DTO\Docs\Types\BooleanType(false);
        $openApiSchema = $this->invokePrivateMethod($this->getSpecificationGenerator(), 'convertSchemaType', [$booleanType]);

        $this->assertInstanceOf(OpenApi\Schema::class, $openApiSchema);

        $expected = [
            'type' => 'boolean',
            'nullable' => false,
        ];

        $this->assertSame($expected, $this->convertOpenApiToArray($openApiSchema));

        // nullable
        $booleanType = new RestApiBundle\DTO\Docs\Types\BooleanType(true);
        $openApiSchema = $this->invokePrivateMethod($this->getSpecificationGenerator(), 'convertSchemaType', [$booleanType]);

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

        $integerType = new RestApiBundle\DTO\Docs\Types\IntegerType(false);
        $integerType
            ->setConstraints([$constraint]);

        $openApiSchema = $this->invokePrivateMethod($this->getSpecificationGenerator(), 'convertSchemaType', [$integerType]);

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
            'choices' => TestApp\Enum\Status::getValues(),
        ]);

        $stringType = new RestApiBundle\DTO\Docs\Types\StringType(false);
        $stringType
            ->setConstraints([$constraint]);

        $openApiSchema = $this->invokePrivateMethod($this->getSpecificationGenerator(), 'convertSchemaType', [$stringType]);

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
            'callback' => 'TestApp\Enum\Status::getValues',
        ]);

        $stringType = new RestApiBundle\DTO\Docs\Types\StringType(false);
        $stringType
            ->setConstraints([$constraint]);

        $openApiSchema = $this->invokePrivateMethod($this->getSpecificationGenerator(), 'convertSchemaType', [$stringType]);

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

        $arrayType = new RestApiBundle\DTO\Docs\Types\ArrayType(new RestApiBundle\DTO\Docs\Types\StringType(false), false);
        $arrayType
            ->setConstraints([$constraint]);

        $openApiSchema = $this->invokePrivateMethod($this->getSpecificationGenerator(), 'convertSchemaType', [$arrayType]);

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

        $stringType = new RestApiBundle\DTO\Docs\Types\StringType(false);
        $stringType
            ->setConstraints([$constraint]);

        $openApiSchema = $this->invokePrivateMethod($this->getSpecificationGenerator(), 'convertSchemaType', [$stringType]);

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
            'offset' => new RestApiBundle\DTO\Docs\Types\IntegerType(false),
            'limit' => new RestApiBundle\DTO\Docs\Types\IntegerType(false),
        ];
        $objectType = new RestApiBundle\DTO\Docs\Types\ObjectType($objectProperties, false);

        /** @var OpenApi\Parameter[] $openApiParameters */
        $openApiParameters = $this->invokePrivateMethod($this->getSpecificationGenerator(), 'convertRequestModelToParameters', [$objectType]);

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
            'offset' => new RestApiBundle\DTO\Docs\Types\IntegerType(false),
            'limit' => new RestApiBundle\DTO\Docs\Types\IntegerType(false),
        ];
        $objectType = new RestApiBundle\DTO\Docs\Types\ObjectType($objectProperties, false);

        /** @var OpenApi\RequestBody $requestBody */
        $requestBody = $this->invokePrivateMethod($this->getSpecificationGenerator(), 'convertRequestModelToRequestBody', [$objectType]);

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
        $dateTimeType = new RestApiBundle\DTO\Docs\Types\DateTimeType(false);
        $openApiSchema = $this->invokePrivateMethod($this->getSpecificationGenerator(), 'convertSchemaType', [$dateTimeType]);

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
        $dateType = new RestApiBundle\DTO\Docs\Types\DateType(false);
        $openApiSchema = $this->invokePrivateMethod($this->getSpecificationGenerator(), 'convertSchemaType', [$dateType]);

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

    private function getSpecificationGenerator(): RestApiBundle\Services\Docs\OpenApi\SpecificationGenerator
    {
        /** @var RestApiBundle\Services\Docs\OpenApi\SpecificationGenerator $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\OpenApi\SpecificationGenerator::class);

        return $result;
    }
}
