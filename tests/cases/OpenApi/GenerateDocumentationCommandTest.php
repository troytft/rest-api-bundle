<?php
declare(strict_types=1);

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class GenerateDocumentationCommandTest extends Tests\BaseTestCase
{
    public function testSuccess()
    {
        $filename = tempnam(sys_get_temp_dir(), 'openapi') . '.yaml';

        $application = new Application($this->getKernel());
        $command = $application->find('rest-api:generate-documentation');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'input' => 'tests/src/Fixture/OpenApi/GenerateDocumentationCommandTest/TestSuccess',
            'output' => $filename,
            '--template' => 'tests/src/Fixture/OpenApi/GenerateDocumentationCommandTest/TestSuccess/Resources/template.yaml'
        ]);

        $this->assertSame(0, $commandTester->getStatusCode(), $commandTester->getDisplay());
        $this->assertMatchesSnapshot(file_get_contents($filename));
    }

    public function testInvalidDefinition()
    {
        $application = new Application($this->getKernel());
        $command = $application->find('rest-api:generate-documentation');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'input' => 'tests/src/Fixture/OpenApi/GenerateDocumentationCommandTest/TestInvalidDefinition',
            'output' => 'openapi.json',
        ]);

        $this->assertSame(1, $commandTester->getStatusCode());
        $this->assertSame("An error occurred:\nAssociated parameter for placeholder unknown_parameter not matched Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestInvalidDefinition\DefaultController->testAction()", trim($commandTester->getDisplay()));
    }
}
