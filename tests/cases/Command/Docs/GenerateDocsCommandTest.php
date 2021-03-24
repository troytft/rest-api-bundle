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
        $this->assertMatchesTextSnapshot(file_get_contents($fileName));
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
        $this->assertMatchesJsonSnapshot(file_get_contents($fileName));
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
