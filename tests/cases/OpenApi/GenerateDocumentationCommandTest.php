<?php

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class GenerateDocumentationCommandTest extends Tests\BaseTestCase
{
    public function testSuccessYaml()
    {
        $fileName = $this->getOutputFileName() . '.yaml';

        $application = new Application($this->getKernel());
        $command = $application->find('rest-api:generate-documentation');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'input' => 'tests/src/Fixture/OpenApi/GenerateDocumentationCommandTest/TestSuccess',
            'output' => $fileName,
            '--template' => 'tests/src/Fixture/OpenApi/GenerateDocumentationCommandTest/TestSuccess/Resources/template.yaml'
        ]);

        $this->assertSame(0, $commandTester->getStatusCode(), $commandTester->getDisplay());
        $this->assertMatchesTextSnapshot(file_get_contents($fileName));
    }

    public function testSuccessJson()
    {
        $fileName = $this->getOutputFileName() . '.json';

        $application = new Application($this->getKernel());
        $command = $application->find('rest-api:generate-documentation');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'input' => 'tests/src/Fixture/OpenApi/GenerateDocumentationCommandTest/TestSuccess',
            'output' => $fileName,
            '--template' => 'tests/src/Fixture/OpenApi/GenerateDocumentationCommandTest/TestSuccess/Resources/template.json'
        ]);

        $this->assertSame(0, $commandTester->getStatusCode(), $commandTester->getDisplay());
        $this->assertMatchesJsonSnapshot(file_get_contents($fileName));
    }

    public function testInvalidDefinition()
    {
        $fileName = $this->getOutputFileName() . '.json';

        $application = new Application($this->getKernel());
        $command = $application->find('rest-api:generate-documentation');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'input' => 'tests/src/Fixture/OpenApi/GenerateDocumentationCommandTest/TestInvalidDefinition',
            'output' => $fileName,
        ]);

        $this->assertSame(1, $commandTester->getStatusCode());
        $this->assertSame("An error occurred:\nAssociated parameter for placeholder unknown_parameter not matched Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestInvalidDefinition\DefaultController->testAction()", trim($commandTester->getDisplay()));
    }

    private function getOutputFileName(): string
    {
        return tempnam(sys_get_temp_dir(), 'openapi');
    }
}
