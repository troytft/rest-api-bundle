<?php

namespace Tests;

use Tests;
use cebe\openapi\spec as OpenApi;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use RestApiBundle;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Nyholm\BundleTest\TestKernel;

abstract class BaseTestCase extends KernelTestCase
{
    use MatchesSnapshots;

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        /** @var TestKernel $kernel */
        $kernel = parent::createKernel($options);
        $kernel->addTestBundle(RestApiBundle\RestApiBundle::class);
        $kernel->addTestBundle(Tests\Fixture\TestApp\TestAppBundle::class);
        $kernel->addTestBundle(DoctrineBundle::class);
        $kernel->addTestConfig(__DIR__ . '/Fixture/TestApp/Resources/config/config.yaml');
        $kernel->handleOptions($options);

        return $kernel;
    }

    public function getKernel(): KernelInterface
    {
        $this->bootKernel();
        $this->setupDatabase();

        return static::$kernel;
    }

    private function setupDatabase(): void
    {
        $entityManager = $this->getContainer()->get(EntityManagerInterface::class);
        $schemaTool = new SchemaTool($entityManager);
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        
        if (!empty($metadata)) {
            $schemaTool->createSchema($metadata);
        }
    }

    protected function assertMatchesOpenApiSchemaSnapshot(OpenApi\Schema|OpenApi\OpenApi $schema): void
    {
        $this->assertMatchesJsonSnapshot(json_encode($schema->getSerializableData()));
    }

    protected function assertMatchesOpenApiSchemaSnapshots(array $schemas): void
    {
        $this->assertMatchesJsonSnapshot(json_encode(array_map(fn (OpenApi\Schema|OpenApi\OpenApi $schema) => $schema->getSerializableData(), $schemas)));
    }

    protected function getMapper(): RestApiBundle\Services\Mapper\Mapper
    {
        return $this->getContainer()->get(RestApiBundle\Services\Mapper\Mapper::class);
    }
}
