<?php

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class GenerateDocsCommandTest extends Tests\BaseTestCase
{
    public function testSuccessYaml()
    {
        $fileName = $this->getOutputFileName();

        $application = new Application($this->getKernel());
        $command = $application->find('rest-api:generate-docs');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'input' => 'tests/test-app/Controller/CommandTest/Success',
            'output' => $fileName,
            '--format' => RestApiBundle\Enum\Docs\Format::YAML,
        ]);

        $this->assertSame(0, $commandTester->getStatusCode());

        $expected = <<<YAML
openapi: 3.0.0
info:
    title: 'Open API Specification'
    version: 1.0.0
paths:
    '/{author}/{slug}/genres':
        get:
            summary: 'Genre response model details'
            responses:
                '200':
                    description: 'Success response with body'
                    content:
                        application/json:
                            schema:
                                type: array
                                items:
                                    type: object
                                    properties:
                                        id:
                                            type: integer
                                            nullable: false
                                        slug:
                                            type: string
                                            nullable: false
                                        __typename:
                                            type: string
                                            nullable: false
                                    nullable: false
                                nullable: false
            parameters:
                -
                    name: author
                    in: path
                    required: true
                    schema:
                        type: integer
                        description: 'Entity "Author" by field "id"'
                        nullable: true
                -
                    name: slug
                    in: path
                    required: true
                    schema:
                        type: string
                        description: 'Entity "Genre" by field "slug"'
                        nullable: true
            tags:
                - demo
tags:
    -
        name: demo

YAML;
        $this->assertSame($expected, file_get_contents($fileName));
    }

    public function testSuccessJson()
    {
        $fileName = $this->getOutputFileName();

        $application = new Application($this->getKernel());
        $command = $application->find('rest-api:generate-docs');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'input' => 'tests/test-app/Controller/CommandTest/Success',
            'output' => $fileName,
            '--format' => RestApiBundle\Enum\Docs\Format::JSON,
        ]);

        $this->assertSame(0, $commandTester->getStatusCode());

        $expected = <<<JSON
{
    "openapi": "3.0.0",
    "info": {
        "title": "Open API Specification",
        "version": "1.0.0"
    },
    "paths": {
        "/{author}/{slug}/genres": {
            "get": {
                "summary": "Genre response model details",
                "responses": {
                    "200": {
                        "description": "Success response with body",
                        "content": {
                            "application/json": {
                                "schema": {
                                    "type": "array",
                                    "items": {
                                        "type": "object",
                                        "properties": {
                                            "id": {
                                                "type": "integer",
                                                "nullable": false
                                            },
                                            "slug": {
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
                                }
                            }
                        }
                    }
                },
                "parameters": [
                    {
                        "name": "author",
                        "in": "path",
                        "required": true,
                        "schema": {
                            "type": "integer",
                            "description": "Entity \"Author\" by field \"id\"",
                            "nullable": true
                        }
                    },
                    {
                        "name": "slug",
                        "in": "path",
                        "required": true,
                        "schema": {
                            "type": "string",
                            "description": "Entity \"Genre\" by field \"slug\"",
                            "nullable": true
                        }
                    }
                ],
                "tags": [
                    "demo"
                ]
            }
        }
    },
    "tags": [
        {
            "name": "demo"
        }
    ]
}
JSON;
        $this->assertSame($expected, file_get_contents($fileName));
    }

    public function testInvalidDefinition()
    {
        $fileName = $this->getOutputFileName();

        $application = new Application($this->getKernel());
        $command = $application->find('rest-api:generate-docs');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'input' => 'tests/test-app/Controller/CommandTest/InvalidDefinition',
            'output' => $fileName,
        ]);

        $this->assertSame(1, $commandTester->getStatusCode());
        $this->assertSame('Definition error in TestApp\Controller\CommandTest\InvalidDefinition\DefaultController::testAction with message "Associated parameter for placeholder unknown_parameter not matched."', trim($commandTester->getDisplay()));
    }

    private function getOutputFileName(): string
    {
        return tempnam(sys_get_temp_dir(), 'openapi');
    }
}
