<?php

namespace RestApiBundle\Helper;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use RestApiBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpKernel\Kernel;

final class AppKernel extends Kernel
{
    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new RestApiBundle\RestApiBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../Resources/config/openapi/services.php');
    }

    public static function createConsoleApplication(): Application
    {
        $kernel = new static('cli', false);

        $application = new Application($kernel);
        $application->setDefaultCommand(RestApiBundle\Command\OpenApi\GenerateDocsCommand::getDefaultName(), true);

        return $application;
    }
}
