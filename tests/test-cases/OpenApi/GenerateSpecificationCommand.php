<?php

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class GenerateSpecificationCommand extends Tests\BaseTestCase
{
    public function testSuccessYaml()
    {
        $fileName = $this->getOutputFileName() . '.yaml';

        $application = new Application($this->getKernel());
        $command = $application->find('rest-api:open-api:generate-specification');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'input' => 'tests/src/Fixture/OpenApi/GenerateSpecificationCommand/Success',
            'output' => $fileName,
            '--template' => 'tests/src/Fixture/OpenApi/GenerateSpecificationCommand/Success/Resources/template.yaml'
        ]);

        $this->assertSame(0, $commandTester->getStatusCode());
        $this->assertMatchesTextSnapshot(file_get_contents($fileName));
    }

    public function testSuccessJson()
    {
        $fileName = $this->getOutputFileName() . '.json';

        $application = new Application($this->getKernel());
        $command = $application->find('rest-api:open-api:generate-specification');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'input' => 'tests/src/Fixture/OpenApi/GenerateSpecificationCommand/Success',
            'output' => $fileName,
            '--template' => 'tests/src/Fixture/OpenApi/GenerateSpecificationCommand/Success/Resources/template.json'
        ]);

        $this->assertSame(0, $commandTester->getStatusCode());
        $this->assertMatchesJsonSnapshot(file_get_contents($fileName));
    }

    public function testInvalidDefinition()
    {
        $fileName = $this->getOutputFileName() . '.json';

        $application = new Application($this->getKernel());
        $command = $application->find('rest-api:open-api:generate-specification');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'input' => 'tests/src/Fixture/OpenApi/GenerateSpecificationCommand/InvalidDefinition',
            'output' => $fileName,
        ]);

        $this->assertSame(1, $commandTester->getStatusCode());
        $this->assertSame("An error occurred:\nAssociated parameter for placeholder unknown_parameter not matched Tests\Fixture\OpenApi\GenerateSpecificationCommand\InvalidDefinition\DefaultController->testAction()", trim($commandTester->getDisplay()));
    }

    private function getOutputFileName(): string
    {
        return tempnam(sys_get_temp_dir(), 'openapi');
    }
}
