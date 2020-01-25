<?php

namespace Tests\Docs;

use Tests;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Yaml\Yaml;

class GenerateDocsCommandTest extends Tests\BaseBundleTestCase
{
    public function testExecute()
    {
        $temporaryOutputFile = tempnam(sys_get_temp_dir(), 'openapi');

        $application = new Application($this->getKernel());
        $command = $application->find('rest-api:generate-docs');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--output' => $temporaryOutputFile,
            '--controller-namespace-prefix' => Tests\DemoApp\DemoBundle\Controller\DemoController::class,
        ]);

        $generatedData = Yaml::parseFile($temporaryOutputFile);

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
}
