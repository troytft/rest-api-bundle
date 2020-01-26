<?php

namespace Tests\Docs;

use Tests;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Yaml\Yaml;
use function str_replace;
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

        $generatedData = Yaml::parseFile($outputFile);

        $this->assertSame(0, $commandTester->getStatusCode());
        $this->assertSame([
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Open API Specification',
                'version' => '1.0.0',
            ],
            'paths' => [
                '/genres/by-slug/{genre}' => [
                    'get' => [
                        'summary' => 'Genre response model details',
                        'responses' => [
                            200 => [
                                'description' => 'Success response with body',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'id' => [
                                                    'type' => 'integer',
                                                    'nullable' => false,
                                                ],
                                                'slug' => [
                                                    'type' => 'string',
                                                    'nullable' => false,
                                                ],
                                                '__typename' => [
                                                    'type' => 'string',
                                                    'nullable' => false,
                                                ],
                                            ],
                                            'nullable' => false,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'parameters' => [
                            [
                                'name' => 'genre',
                                'in' => 'path',
                                'description' => 'String regex format is "\\d+".',
                                'required' => true,
                                'schema' => [
                                    'type' => 'string',
                                    'nullable' => false,
                                ],
                            ],
                        ],
                        'tags' => [
                            'demo',
                        ],
                    ],
                ],
            ],
            'tags' => [
                [
                    'name' => 'demo',
                ],
            ],
        ], $generatedData);
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

        $commandDisplay = str_replace("\n", '', $commandTester->getDisplay());

        $this->assertSame(100, $commandTester->getStatusCode());
        $this->assertStringContainsString('[ERROR] Error: Return type not found in docBlock and type-hint.', $commandDisplay);
        $this->assertStringContainsString('Controller: Tests\DemoApp\DemoBundle\Controller\InvalidDefinition\UnknownReturnTypeController', $commandDisplay);
        $this->assertStringContainsString('Action: getGenreAction ', $commandDisplay);
    }

    private function getOutputFile(): string
    {
        return tempnam(sys_get_temp_dir(), 'openapi');
    }
}
