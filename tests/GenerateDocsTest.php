<?php

namespace Tests;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use function var_dump;

class GenerateDocsTest extends BaseBundleTestCase
{
    public function testExecute()
    {
        $application = new Application($this->getKernel());
        $command = $application->find('rest-api:generate-docs');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        var_dump($commandTester->getStatusCode(), $commandTester->getDisplay());
    }
}
