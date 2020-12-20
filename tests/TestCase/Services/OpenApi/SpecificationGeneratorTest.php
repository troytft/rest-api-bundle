<?php

namespace Tests\TestCase\Services\OpenApi;

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
        $schema = new RestApiBundle\DTO\OpenApi\Schema\StringType(false);
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
        $booleanType = new RestApiBundle\DTO\OpenApi\Schema\BooleanType(false);
        $openApiSchema = $this->invokePrivateMethod($this->getSpecificationGenerator(), 'convertSchemaType', [$booleanType]);

        $this->assertInstanceOf(OpenApi\Schema::class, $openApiSchema);

        $expected = [
            'type' => 'boolean',
            'nullable' => false,
        ];

        $this->assertSame($expected, $this->convertOpenApiToArray($openApiSchema));

        // nullable
        $booleanType = new RestApiBundle\DTO\OpenApi\Schema\BooleanType(true);
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

        $integerType = new RestApiBundle\DTO\OpenApi\Schema\IntegerType(false);
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
            'choices' => Tests\TestApp\TestBundle\Enum\Status::getValues(),
        ]);

        $stringType = new RestApiBundle\DTO\OpenApi\Schema\StringType(false);
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
            'callback' => 'Tests\TestApp\TestBundle\Enum\Status::getValues',
        ]);

        $stringType = new RestApiBundle\DTO\OpenApi\Schema\StringType(false);
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

        $arrayType = new RestApiBundle\DTO\OpenApi\Schema\ArrayType(new RestApiBundle\DTO\OpenApi\Schema\StringType(false), false);
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

        $stringType = new RestApiBundle\DTO\OpenApi\Schema\StringType(false);
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
        $classType = new RestApiBundle\DTO\OpenApi\Schema\ClassType(Tests\TestApp\TestBundle\RequestModel\InnerModelWithValidation::class, false);

        /** @var OpenApi\Parameter[] $openApiParameters */
        $openApiParameters = $this->invokePrivateMethod($this->getSpecificationGenerator(), 'convertRequestModelToParameters', [$classType]);

        $this->assertIsArray($openApiParameters);
        $this->assertCount(1, $openApiParameters);

        $this->assertInstanceOf(OpenApi\Parameter::class, $openApiParameters[0]);
        $this->assertSame([
            'name' => 'stringField',
            'in' => 'query',
            'required' => true,
            'schema' => [
                'type' => 'string',
                'nullable' => false,
                'minLength' => 3,
                'maxLength' => 255,
            ],
        ], $this->convertOpenApiToArray($openApiParameters[0]));
    }

    public function testConvertRequestModelToRequestBody()
    {
        $classType = new RestApiBundle\DTO\OpenApi\Schema\ClassType(Tests\TestApp\TestBundle\RequestModel\InnerModelWithValidation::class, false);

        /** @var OpenApi\RequestBody $requestBody */
        $requestBody = $this->invokePrivateMethod($this->getSpecificationGenerator(), 'convertRequestModelToRequestBody', [$classType]);

        $expected = [
            'description' => 'Request body',
            'content' => [
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'stringField' => [
                                'type' => 'string',
                                'nullable' => false,
                                'minLength' => 3,
                                'maxLength' => 255,
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
        $dateTimeType = new RestApiBundle\DTO\OpenApi\Schema\DateTimeType(false);
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
        $dateType = new RestApiBundle\DTO\OpenApi\Schema\DateType(false);
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

    private function getSpecificationGenerator(): RestApiBundle\Services\OpenApi\SpecificationGenerator
    {
        return $this->getContainer()->get(RestApiBundle\Services\OpenApi\SpecificationGenerator::class);
    }
}
