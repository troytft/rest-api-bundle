<?php

namespace Tests\TestCase\Services\OpenApi;

use Tests;
use RestApiBundle;
use function array_keys;
use function json_encode;
use function var_dump;

class ResponseModelSchemaResolverTest extends Tests\TestCase\BaseTestCase
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

        $schema = $this->getResponseModelSchemaResolver()->resolveByClass(Tests\TestApp\TestBundle\ResponseModel\ModelWithTypeHint::class);
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

        $schema = $this->getResponseModelSchemaResolver()->resolveByClass(Tests\TestApp\TestBundle\ResponseModel\ModelWithDocBlock::class);
        $this->assertJsonStringEqualsJsonString($expected, json_encode($schema->getSerializableData()));
    }
}
