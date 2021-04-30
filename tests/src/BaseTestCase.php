<?php

namespace Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use RestApiBundle;
use Spatie\Snapshots\MatchesSnapshots;
use TestApp;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class BaseTestCase extends \Nyholm\BundleTest\BaseBundleTestCase
{
    use MatchesSnapshots;

    /**
     * @var KernelInterface
     */
    protected $kernel;

    public function __construct()
    {
        parent::__construct();

        $this->bootKernel();
    }

    protected function getBundleClass()
    {
        return RestApiBundle\RestApiBundle::class;
    }

    protected function createKernel()
    {
        $this->kernel = parent::createKernel();
        $this->kernel->setRootDir(__DIR__ . '/../test-app');
        $this->kernel->addBundle(TestApp\TestAppBundle::class);
        $this->kernel->addBundle(DoctrineBundle::class);
        $this->kernel->addConfigFile(__DIR__ . '/../test-app/Resources/config/config.yaml');

        return $this->kernel;
    }

    public function getKernel(): KernelInterface
    {
        return $this->kernel;
    }
}
