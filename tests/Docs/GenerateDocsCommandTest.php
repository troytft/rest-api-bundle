<?php

namespace Tests\Docs;

use Tests;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Yaml\Yaml;
use function file_get_contents;
use function var_dump;

class GenerateDocsCommandTest extends Tests\BaseBundleTestCase
{
    public function testExecute()
    {
        $temporaryOutputFile = tempnam(sys_get_temp_dir(), 'openapi');

        $application = new Application($this->getKernel());
        $command = $application->find('rest-api:generate-docs');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['--output' => $temporaryOutputFile]);

        var_dump(file_get_contents($temporaryOutputFile));
        $generatedData = Yaml::parseFile($temporaryOutputFile);
        $preparedData = Yaml::parseFile(__DIR__ . '/../data/openapi.yaml');

        $this->assertSame(0, $commandTester->getStatusCode());
        $this->assertSame($preparedData, $generatedData);
    }
}
