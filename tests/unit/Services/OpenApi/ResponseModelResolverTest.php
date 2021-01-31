<?php

class ResponseModelResolverTest extends Tests\BaseTestCase
{
    public function testSchemaFromTypeHints()
    {
        $expected = <<<JSON
{
    "type": "object",
    "properties": {
        "stringField": {
            "type": "string",
            "nullable": false
        },
        "nullableStringField": {
            "type": "string",
            "nullable": true
        },
        "dateTimeField": {
            "type": "string",
            "format": "date-time",
            "nullable": false
        },
        "modelField": {
            "type": "object",
            "properties": {
                "stringFieldWithTypeHint": {
                    "type": "string",
                    "nullable": false
                },
                "stringFieldWithDocBlock": {
                    "type": "string",
                    "nullable": false
                },
                "__typename": {
                    "type": "string",
                    "nullable": false
                }
            },
            "nullable": false
        },
        "__typename": {
            "type": "string",
            "nullable": false
        }
    },
    "nullable": false
}
JSON;

        $schema = $this->getResponseModelResolver()->resolveByClass(TestApp\ResponseModel\ModelWithTypeHint::class);
        $this->assertJsonStringEqualsJsonString($expected, json_encode($schema->getSerializableData()));
    }

    public function testSchemaFromDocBlocks()
    {
        $expected = <<<JSON
{
    "type": "object",
    "properties": {
        "stringField": {
            "type": "string",
            "nullable": false
        },
        "nullableStringField": {
            "type": "string",
            "nullable": true
        },
        "dateTimeField": {
            "type": "string",
            "format": "date-time",
            "nullable": false
        },
        "modelField": {
            "type": "object",
            "properties": {
                "stringFieldWithTypeHint": {
                    "type": "string",
                    "nullable": false
                },
                "stringFieldWithDocBlock": {
                    "type": "string",
                    "nullable": false
                },
                "__typename": {
                    "type": "string",
                    "nullable": false
                }
            },
            "nullable": false
        },
                "arrayOfModelsField": {
            "type": "array",
            "items": {
                "type": "object",
                "properties": {
                    "stringFieldWithTypeHint": {
                        "type": "string",
                        "nullable": false
                    },
                    "stringFieldWithDocBlock": {
                        "type": "string",
                        "nullable": false
                    },
                    "__typename": {
                        "type": "string",
                        "nullable": false
                    }
                },
                "nullable": false
            },
            "nullable": false
        },
        "__typename": {
            "type": "string",
            "nullable": false
        }
    },
    "nullable": false
}
JSON;

        $schema = $this->getResponseModelResolver()->resolveByClass(TestApp\ResponseModel\ModelWithDocBlock::class);
        $this->assertJsonStringEqualsJsonString($expected, json_encode($schema->getSerializableData()));
    }

    protected function getResponseModelResolver(): RestApiBundle\Services\Docs\OpenApi\ResponseModelResolver
    {
        /** @var RestApiBundle\Services\Docs\OpenApi\ResponseModelResolver $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\OpenApi\ResponseModelResolver::class);

        return $result;
    }
}
