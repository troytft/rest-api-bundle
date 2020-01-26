<?php

namespace Tests\Docs;

use Tests;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use function file_get_contents;
use function sys_get_temp_dir;
use function tempnam;

class GenerateDocsCommandTest extends Tests\BaseBundleTestCase
{
    public function testSuccess()
    {
        $outputFile = $this->getOutputFile();

        $application = new Application($this->getKernel());
        $command = $application->find('rest-api:generate-docs');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--output' => $outputFile,
            '--controller-namespace-prefix' => Tests\DemoApp\DemoBundle\Controller\DemoController::class,
        ]);

        $this->assertSame(0, $commandTester->getStatusCode());

        $expextedYaml = <<<YAML
openapi: 3.0.0
info:
    title: 'Open API Specification'
    version: 1.0.0
paths:
    '/genres/by-slug/{genre}':
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
                    name: genre
                    in: path
                    description: 'String regex format is "\d+".'
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

        $this->assertSame($expextedYaml, file_get_contents($outputFile));
    }

    public function testInvalidDefinitionError()
    {
        $outputFile = $this->getOutputFile();

        $application = new Application($this->getKernel());
        $command = $application->find('rest-api:generate-docs');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--output' => $outputFile,
            '--controller-namespace-prefix' => Tests\DemoApp\DemoBundle\Controller\InvalidDefinition\UnknownReturnTypeController::class,
        ]);

        $this->assertSame(100, $commandTester->getStatusCode());
        $this->assertStringContainsString('Message: Return type not found in docBlock and type-hint.', $commandTester->getDisplay());
        $this->assertStringContainsString('Controller: Tests\DemoApp\DemoBundle\Controller\InvalidDefinition\UnknownReturnTypeController', $commandTester->getDisplay());
        $this->assertStringContainsString('Action: getGenreAction', $commandTester->getDisplay());
    }

    private function getOutputFile(): string
    {
        return tempnam(sys_get_temp_dir(), 'openapi');
    }
}
