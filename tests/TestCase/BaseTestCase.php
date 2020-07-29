<?php

namespace Tests\TestCase;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use RestApiBundle;
use Tests\TestApp;
use Symfony\Component\HttpKernel\KernelInterface;
use function get_class;

abstract class BaseTestCase extends \Nyholm\BundleTest\BaseBundleTestCase
{
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
        $this->kernel->setRootDir(__DIR__ . '/../TestApp');
        $this->kernel->addBundle(TestApp\TestBundle\TestBundle::class);
        $this->kernel->addBundle(DoctrineBundle::class);
        $this->kernel->addConfigFile(__DIR__ . '/../TestApp/config/config.yaml');

        return $this->kernel;
    }

    public function getKernel(): KernelInterface
    {
        return $this->kernel;
    }

    protected function getResponseSerializer(): RestApiBundle\Services\Response\Serializer
    {
        $result = $this->getContainer()->get(RestApiBundle\Services\Response\Serializer::class);
        if (!$result instanceof RestApiBundle\Services\Response\Serializer) {
            throw new \InvalidArgumentException();
        }

        return $result;
    }

    protected function getRequestModelManager(): RestApiBundle\Services\Request\RequestHandler
    {
        $result = $this->getContainer()->get(RestApiBundle\Services\Request\RequestHandler::class);
        if (!$result instanceof RestApiBundle\Services\Request\RequestHandler) {
            throw new \InvalidArgumentException();
        }

        return $result;
    }

    protected function invokePrivateMethod($object, string $methodName, array $parameters = [])
    {
        $reflection = RestApiBundle\Services\ReflectionClassStore::get(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    protected function getEndpointFinder(): RestApiBundle\Services\Docs\EndpointFinder
    {
        /** @var RestApiBundle\Services\Docs\EndpointFinder $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\EndpointFinder::class);

        return $result;
    }

    protected function getRequestModelValidator(): RestApiBundle\Services\Request\RequestModelValidator
    {
        /** @var RestApiBundle\Services\Request\RequestModelValidator $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Request\RequestModelValidator::class);

        return $result;
    }

    protected function getDocBlockSchemaReader(): RestApiBundle\Services\Docs\Schema\DocBlockReader
    {
        /** @var RestApiBundle\Services\Docs\Schema\DocBlockReader $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\Schema\DocBlockReader::class);

        return $result;
    }

    protected function getRequestModelHelper(): RestApiBundle\Services\Docs\RequestModelHelper
    {
        /** @var RestApiBundle\Services\Docs\RequestModelHelper $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\RequestModelHelper::class);

        return $result;
    }

    protected function getResponseCollector(): RestApiBundle\Services\Docs\ResponseCollector
    {
        /** @var RestApiBundle\Services\Docs\ResponseCollector $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\ResponseCollector::class);

        return $result;
    }

    protected function getTypeHintSchemaReader(): RestApiBundle\Services\Docs\Schema\TypeHintReader
    {
        /** @var RestApiBundle\Services\Docs\Schema\TypeHintReader $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\Schema\TypeHintReader::class);

        return $result;
    }

    protected function getOpenApiSpecificationGenerator(): RestApiBundle\Services\Docs\OpenApi\SpecificationGenerator
    {
        /** @var RestApiBundle\Services\Docs\OpenApi\SpecificationGenerator $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\OpenApi\SpecificationGenerator::class);

        return $result;
    }
}
