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
            '--template' => 'tests/test-app/Resources/docs/swagger.yaml'
        ]);

        $this->assertSame(0, $commandTester->getStatusCode());

        $expected = <<<YAML
openapi: 3.0.0
info:
    title: 'Yaml Template'
    version: 1.0.0
paths:
    /books:
        get:
            summary: 'Books list'
            responses:
                '200':
                    description: 'Success response with body'
                    content:
                        application/json:
                            schema:
                                type: array
                                items:
                                    \$ref: '#/components/schemas/Book'
                                nullable: false
            tags:
                - books
    /writers:
        post:
            summary: 'Create writer'
            responses:
                '200':
                    description: 'Success response with body'
                    content:
                        application/json:
                            schema:
                                \$ref: '#/components/schemas/Author'
            requestBody:
                description: 'Request body'
                content:
                    application/json:
                        schema:
                            type: object
                            properties:
                                name:
                                    type: string
                                    nullable: false
                                    minLength: 1
                                    maxLength: 255
                                surname:
                                    type: string
                                    nullable: false
                                    minLength: 1
                                    maxLength: 255
                                birthday:
                                    type: string
                                    format: date
                                    nullable: true
                                genres:
                                    type: array
                                    items:
                                        type: integer
                                        nullable: false
                                    description: 'Array of elements by "id"'
                                    nullable: false
                            nullable: false
                required: true
            tags:
                - writers
        get:
            summary: 'Writers list with filters'
            responses:
                '200':
                    description: 'Success response with body'
                    content:
                        application/json:
                            schema:
                                type: array
                                items:
                                    \$ref: '#/components/schemas/Author'
                                nullable: false
            parameters:
                -
                    name: offset
                    in: query
                    required: true
                    schema:
                        type: integer
                        nullable: false
                -
                    name: limit
                    in: query
                    required: true
                    schema:
                        type: integer
                        nullable: false
                -
                    name: name
                    in: query
                    required: false
                    schema:
                        type: string
                        nullable: true
                        minLength: 1
                        maxLength: 255
                -
                    name: surname
                    in: query
                    required: false
                    schema:
                        type: string
                        nullable: true
                        minLength: 1
                        maxLength: 255
                -
                    name: birthday
                    in: query
                    required: false
                    schema:
                        type: string
                        format: date
                        nullable: true
                -
                    name: genres
                    in: query
                    required: false
                    schema:
                        type: array
                        items:
                            type: integer
                            nullable: false
                        nullable: true
                    description: 'Array of elements by "id"'
                -
                    name: writer
                    in: query
                    required: false
                    schema:
                        type: integer
                        nullable: true
                    description: 'Element by "id"'
            tags:
                - writers
    '/writers/{id}':
        delete:
            summary: 'Remove writer'
            responses:
                '204':
                    description: 'Success response with empty body'
            parameters:
                -
                    name: id
                    in: path
                    required: true
                    schema:
                        type: integer
                        description: 'Element by "id"'
                        nullable: false
            tags:
                - writers
components:
    schemas:
        Author:
            type: object
            properties:
                id:
                    type: integer
                    nullable: false
                name:
                    type: string
                    nullable: false
                surname:
                    type: string
                    nullable: false
                birthday:
                    type: string
                    format: date-time
                    nullable: true
                genres:
                    type: array
                    items:
                        \$ref: '#/components/schemas/Genre'
                    nullable: false
                __typename:
                    type: string
                    nullable: false
        Book:
            type: object
            properties:
                id:
                    type: integer
                    nullable: false
                title:
                    type: string
                    nullable: false
                author:
                    \$ref: '#/components/schemas/Author'
                genre:
                    anyOf:
                        -
                            \$ref: '#/components/schemas/Genre'
                    nullable: true
                status:
                    enum:
                        - created
                        - published
                        - archived
                    type: string
                    nullable: false
                __typename:
                    type: string
                    nullable: false
        Genre:
            type: object
            properties:
                id:
                    type: integer
                    nullable: false
                __typename:
                    type: string
                    nullable: false
tags:
    -
        name: books
        description: Books
    -
        name: writers
        description: Writers

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
            '--template' => 'tests/test-app/Resources/docs/swagger.json'
        ]);

        $this->assertSame(0, $commandTester->getStatusCode());

        $expected = <<<JSON
{
    "openapi": "3.0.0",
    "info": {
        "title": "Json Template",
        "version": "1.0.0"
    },
    "paths": {
        "/books": {
            "get": {
                "summary": "Books list",
                "responses": {
                    "200": {
                        "description": "Success response with body",
                        "content": {
                            "application/json": {
                                "schema": {
                                    "type": "array",
                                    "items": {
                                        "\$ref": "#/components/schemas/Book"
                                    },
                                    "nullable": false
                                }
                            }
                        }
                    }
                },
                "tags": [
                    "books"
                ]
            }
        },
        "/writers": {
            "post": {
                "summary": "Create writer",
                "responses": {
                    "200": {
                        "description": "Success response with body",
                        "content": {
                            "application/json": {
                                "schema": {
                                    "\$ref": "#/components/schemas/Author"
                                }
                            }
                        }
                    }
                },
                "requestBody": {
                    "description": "Request body",
                    "content": {
                        "application/json": {
                            "schema": {
                                "type": "object",
                                "properties": {
                                    "name": {
                                        "type": "string",
                                        "nullable": false,
                                        "minLength": 1,
                                        "maxLength": 255
                                    },
                                    "surname": {
                                        "type": "string",
                                        "nullable": false,
                                        "minLength": 1,
                                        "maxLength": 255
                                    },
                                    "birthday": {
                                        "type": "string",
                                        "format": "date",
                                        "nullable": true
                                    },
                                    "genres": {
                                        "type": "array",
                                        "items": {
                                            "type": "integer",
                                            "nullable": false
                                        },
                                        "description": "Array of elements by \"id\"",
                                        "nullable": false
                                    }
                                },
                                "nullable": false
                            }
                        }
                    },
                    "required": true
                },
                "tags": [
                    "writers"
                ]
            },
            "get": {
                "summary": "Writers list with filters",
                "responses": {
                    "200": {
                        "description": "Success response with body",
                        "content": {
                            "application/json": {
                                "schema": {
                                    "type": "array",
                                    "items": {
                                        "\$ref": "#/components/schemas/Author"
                                    },
                                    "nullable": false
                                }
                            }
                        }
                    }
                },
                "parameters": [
                    {
                        "name": "offset",
                        "in": "query",
                        "required": true,
                        "schema": {
                            "type": "integer",
                            "nullable": false
                        }
                    },
                    {
                        "name": "limit",
                        "in": "query",
                        "required": true,
                        "schema": {
                            "type": "integer",
                            "nullable": false
                        }
                    },
                    {
                        "name": "name",
                        "in": "query",
                        "required": false,
                        "schema": {
                            "type": "string",
                            "nullable": true,
                            "minLength": 1,
                            "maxLength": 255
                        }
                    },
                    {
                        "name": "surname",
                        "in": "query",
                        "required": false,
                        "schema": {
                            "type": "string",
                            "nullable": true,
                            "minLength": 1,
                            "maxLength": 255
                        }
                    },
                    {
                        "name": "birthday",
                        "in": "query",
                        "required": false,
                        "schema": {
                            "type": "string",
                            "format": "date",
                            "nullable": true
                        }
                    },
                    {
                        "name": "genres",
                        "in": "query",
                        "required": false,
                        "schema": {
                            "type": "array",
                            "items": {
                                "type": "integer",
                                "nullable": false
                            },
                            "nullable": true
                        },
                        "description": "Array of elements by \"id\""
                    },
                    {
                        "name": "writer",
                        "in": "query",
                        "required": false,
                        "schema": {
                            "type": "integer",
                            "nullable": true
                        },
                        "description": "Element by \"id\""
                    }
                ],
                "tags": [
                    "writers"
                ]
            }
        },
        "/writers/{id}": {
            "delete": {
                "summary": "Remove writer",
                "responses": {
                    "204": {
                        "description": "Success response with empty body"
                    }
                },
                "parameters": [
                    {
                        "name": "id",
                        "in": "path",
                        "required": true,
                        "schema": {
                            "type": "integer",
                            "description": "Element by \"id\"",
                            "nullable": false
                        }
                    }
                ],
                "tags": [
                    "writers"
                ]
            }
        }
    },
    "components": {
        "schemas": {
            "Author": {
                "type": "object",
                "properties": {
                    "id": {
                        "type": "integer",
                        "nullable": false
                    },
                    "name": {
                        "type": "string",
                        "nullable": false
                    },
                    "surname": {
                        "type": "string",
                        "nullable": false
                    },
                    "birthday": {
                        "type": "string",
                        "format": "date-time",
                        "nullable": true
                    },
                    "genres": {
                        "type": "array",
                        "items": {
                            "\$ref": "#/components/schemas/Genre"
                        },
                        "nullable": false
                    },
                    "__typename": {
                        "type": "string",
                        "nullable": false
                    }
                }
            },
            "Book": {
                "type": "object",
                "properties": {
                    "id": {
                        "type": "integer",
                        "nullable": false
                    },
                    "title": {
                        "type": "string",
                        "nullable": false
                    },
                    "author": {
                        "\$ref": "#/components/schemas/Author"
                    },
                    "genre": {
                        "anyOf": [
                            {
                                "\$ref": "#/components/schemas/Genre"
                            }
                        ],
                        "nullable": true
                    },
                    "status": {
                        "enum": [
                            "created",
                            "published",
                            "archived"
                        ],
                        "type": "string",
                        "nullable": false
                    },
                    "__typename": {
                        "type": "string",
                        "nullable": false
                    }
                }
            },
            "Genre": {
                "type": "object",
                "properties": {
                    "id": {
                        "type": "integer",
                        "nullable": false
                    },
                    "__typename": {
                        "type": "string",
                        "nullable": false
                    }
                }
            }
        }
    },
    "tags": [
        {
            "name": "books",
            "description": "Books"
        },
        {
            "name": "writers",
            "description": "Writers"
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
