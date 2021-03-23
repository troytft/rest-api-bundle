<?php

class ResponseModelResolverTest extends Tests\BaseTestCase
{
    public function testSchemaFromTypeHints()
    {
        $reference = $this->getResponseModelResolver()->resolveReferenceByClass(TestApp\ResponseModel\ModelWithTypeHint::class);
        $this->assertJsonStringEqualsJsonString('{"$ref":"#\/components\/schemas\/ModelWithTypeHint"}', json_encode($reference->getSerializableData()));

        $schemas = $this->getResponseModelResolver()->dumpSchemas();

        $this->assertCount(2, $schemas);
        $this->assertArrayHasKey('ModelWithTypeHint', $schemas);
        $this->assertArrayHasKey('CombinedModel', $schemas);

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
            "\$ref": "#/components/schemas/CombinedModel"
        },
        "enumType": {
            "enum": [
                "first",
                "second",
                "third"
            ],
            "nullable": false,
            "type": "string"
        },
        "__typename": {
            "type": "string",
            "nullable": false
        }
    }
}
JSON;
        $this->assertJsonStringEqualsJsonString($expected, json_encode($schemas['ModelWithTypeHint']->getSerializableData()));

        $expected = <<<JSON
{
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
    }
}
JSON;
        $this->assertJsonStringEqualsJsonString($expected, json_encode($schemas['CombinedModel']->getSerializableData()));
    }

    public function testSchemaFromDocBlocks()
    {
        $reference = $this->getResponseModelResolver()->resolveReferenceByClass(TestApp\ResponseModel\ModelWithDocBlock::class);
        $this->assertJsonStringEqualsJsonString('{"$ref":"#\/components\/schemas\/ModelWithDocBlock"}', json_encode($reference->getSerializableData()));

        $schemas = $this->getResponseModelResolver()->dumpSchemas();

        $this->assertCount(2, $schemas);
        $this->assertArrayHasKey('ModelWithDocBlock', $schemas);
        $this->assertArrayHasKey('CombinedModel', $schemas);

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
            "\$ref": "#/components/schemas/CombinedModel"
        },
        "arrayOfModelsField": {
            "type": "array",
            "items": {
                "\$ref": "#/components/schemas/CombinedModel"
            },
            "nullable": false
        },
        "enumType": {
            "enum": [
                "first",
                "second",
                "third"
            ],
            "nullable": false,
            "type": "string"
        },
        "__typename": {
            "type": "string",
            "nullable": false
        }
    }
}
JSON;
        $this->assertJsonStringEqualsJsonString($expected, json_encode($schemas['ModelWithDocBlock']->getSerializableData()));

        $expected = <<<JSON
{
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
    }
}
JSON;
        $this->assertJsonStringEqualsJsonString($expected, json_encode($schemas['CombinedModel']->getSerializableData()));
    }

    protected function getResponseModelResolver(): RestApiBundle\Services\Docs\OpenApi\ResponseModelResolver
    {
        /** @var RestApiBundle\Services\Docs\OpenApi\ResponseModelResolver $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\OpenApi\ResponseModelResolver::class);

        return $result;
    }
}
