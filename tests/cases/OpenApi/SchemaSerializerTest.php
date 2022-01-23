<?php

class SchemaSerializerTest extends Tests\BaseTestCase
{
    public function testJson(): void
    {
        $original = <<<JSON
{
    "openapi": "3.0.0",
    "info": {
        "title": "Open API Specification",
        "version": "1.0.0"
    },
    "paths": {},
    "components": {},
    "tags": []
}
JSON;

        $schema = $this->getSchemaSerializer()->fromJson($original);
        $generated = $this->getSchemaSerializer()->toJson($schema);

        $this->assertSame($original, $generated);
    }
    public function testYaml(): void
    {
        $original = <<<YAML
openapi: 3.0.0
info:
    title: 'Open API Specification'
    version: 1.0.0
paths: {  }
components: {  }
tags: {  }

YAML;

        $schema = $this->getSchemaSerializer()->fromYaml($original);
        $generated = $this->getSchemaSerializer()->toYaml($schema);

        $this->assertSame($original, $generated);
    }

    private function getSchemaSerializer(): RestApiBundle\Services\OpenApi\SchemaSerializer
    {
        return $this->getContainer()->get(RestApiBundle\Services\OpenApi\SchemaSerializer::class);
    }
}
