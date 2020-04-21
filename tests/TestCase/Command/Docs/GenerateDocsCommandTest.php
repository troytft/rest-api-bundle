<?php

namespace Tests\TestCase\Command\Docs;

use Tests;
use RestApiBundle;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use function trim;

class GenerateDocsCommandTest extends Tests\TestCase\BaseTestCase
{
    public function testSuccessYaml()
    {
        $application = new Application($this->getKernel());
        $command = $application->find('rest-api:generate-docs');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--namespace-filter' => Tests\TestApp\TestBundle\Controller\DemoController::class,
            '--format' => RestApiBundle\Enum\Docs\Format::YAML,
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
        $this->assertSame($expected, $commandTester->getDisplay());
    }

    public function testSuccessJson()
    {
        $application = new Application($this->getKernel());
        $command = $application->find('rest-api:generate-docs');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--namespace-filter' => Tests\TestApp\TestBundle\Controller\DemoController::class,
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
        $this->assertSame($expected, $commandTester->getDisplay());
    }

    public function testInvalidDefinition()
    {
        $application = new Application($this->getKernel());
        $command = $application->find('rest-api:generate-docs');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--namespace-filter' => Tests\TestApp\TestBundle\Controller\PathParameters\EmptyRouteRequirementsController::class,
        ]);

        $this->assertSame(1, $commandTester->getStatusCode());
        $this->assertSame('Definition error in Tests\TestApp\TestBundle\Controller\PathParameters\EmptyRouteRequirementsController::testAction with message "Associated parameter for placeholder unknown_parameter not matched."', trim($commandTester->getDisplay()));
    }
}
