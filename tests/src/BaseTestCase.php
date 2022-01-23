<?php

namespace Tests;

use Tests;
use cebe\openapi\spec as OpenApi;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use RestApiBundle;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class BaseTestCase extends \Nyholm\BundleTest\BaseBundleTestCase
{
    use MatchesSnapshots;

    /**
     * @var KernelInterface
     */
    private $kernel;

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
        $this->kernel->setRootDir(__DIR__ . '/Fixture/TestApp');
        $this->kernel->addBundle(Tests\Fixture\TestApp\TestAppBundle::class);
        $this->kernel->addBundle(DoctrineBundle::class);
        $this->kernel->addConfigFile(__DIR__ . '/Fixture/TestApp/Resources/config/config.yaml');

        return $this->kernel;
    }

    public function getKernel(): KernelInterface
    {
        return $this->kernel;
    }

    protected function assertMatchesOpenApiSchemaSnapshot(OpenApi\Schema|OpenApi\OpenApi $schema): void
    {
        $this->assertMatchesJsonSnapshot(json_encode($schema->getSerializableData()));
    }

    protected function getMapper(): RestApiBundle\Services\Mapper\Mapper
    {
        return $this->getContainer()->get(RestApiBundle\Services\Mapper\Mapper::class);
    }
}
