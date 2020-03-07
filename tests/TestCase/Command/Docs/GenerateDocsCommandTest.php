<?php

namespace Tests\TestCase\Command\Docs;

use Tests;
use RestApiBundle;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use function file_get_contents;
use function sys_get_temp_dir;
use function tempnam;
use function var_dump;

class GenerateDocsCommandTest extends Tests\TestCase\BaseTestCase
{
    public function testSuccessYaml()
    {
        $fileName = $this->getOutputFileName();

        $application = new Application($this->getKernel());
        $command = $application->find('rest-api:generate-docs');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'output' => $fileName,
            '--namespace-filter' => Tests\TestApp\TestBundle\Controller\DemoController::class,
        ]);

        $this->assertSame(0, $commandTester->getStatusCode());

        $expected = <<<YAML
openapi: 3.0.0
info:
    title: 'Open API Specification'
    version: 1.0.0
paths:
    '/genres/by-slug/{slug}':
        get:
            summary: 'Genre response model details'
            responses:
                '200':
                    description: 'Success response with body'
                    content:
                        application/json:
                            schema:
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
            parameters:
                -
                    name: slug
                    in: path
                    required: true
                    schema:
                        type: string
                        nullable: false
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
            'output' => $fileName,
            '--namespace-filter' => Tests\TestApp\TestBundle\Controller\DemoController::class,
            '--file-format' => RestApiBundle\Enum\Docs\FileFormat::JSON,
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
        "/genres/by-slug/{slug}": {
            "get": {
                "summary": "Genre response model details",
                "responses": {
                    "200": {
                        "description": "Success response with body",
                        "content": {
                            "application/json": {
                                "schema": {
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
                                }
                            }
                        }
                    }
                },
                "parameters": [
                    {
                        "name": "slug",
                        "in": "path",
                        "required": true,
                        "schema": {
                            "type": "string",
                            "nullable": false
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

    private function getOutputFileName(): string
    {
        return tempnam(sys_get_temp_dir(), 'openapi');
    }
}
