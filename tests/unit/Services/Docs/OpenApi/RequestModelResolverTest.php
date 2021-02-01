<?php

class RequestModelResolverTest extends Tests\BaseTestCase
{
    public function testNestedModelWithConstraints()
    {
        $expected = <<<JSON
{
    "type": "object",
    "properties": {
        "stringField": {
            "type": "string",
            "nullable": false,
            "minLength": 6,
            "maxLength": 255
        },
        "modelField": {
            "type": "object",
            "properties": {
                "stringField": {
                    "type": "string",
                    "nullable": false,
                    "minLength": 3,
                    "maxLength": 255
                }
            },
            "nullable": false
        },
        "collectionField": {
            "type": "array",
            "items": {
                "type": "object",
                "properties": {
                    "stringField": {
                        "type": "string",
                        "nullable": false,
                        "minLength": 3,
                        "maxLength": 255
                    }
                },
                "nullable": false
            },
            "nullable": false
        },
        "collectionOfIntegers": {
            "type": "array",
            "items": {
                "type": "integer",
                "nullable": false
            },
            "nullable": false
        }
    },
    "nullable": false
}
JSON;

        $schema = $this->getRequestModelResolver()->resolveByClass(TestApp\RequestModel\ModelWithValidation::class);
        $this->assertJsonStringEqualsJsonString($expected, json_encode($schema->getSerializableData()));
    }

    private function getRequestModelResolver(): RestApiBundle\Services\Docs\OpenApi\RequestModelResolver
    {
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\OpenApi\RequestModelResolver::class);
        if (!$result instanceof RestApiBundle\Services\Docs\OpenApi\RequestModelResolver) {
            throw new \InvalidArgumentException();
        }

        return $result;
    }
}
