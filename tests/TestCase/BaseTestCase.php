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

    protected function getEndpointFinder(): RestApiBundle\Services\OpenApi\EndpointFinder
    {
        /** @var RestApiBundle\Services\OpenApi\EndpointFinder $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\OpenApi\EndpointFinder::class);

        return $result;
    }

    protected function getRequestModelValidator(): RestApiBundle\Services\Request\RequestModelValidator
    {
        /** @var RestApiBundle\Services\Request\RequestModelValidator $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Request\RequestModelValidator::class);

        return $result;
    }

    protected function getDocBlockSchemaReader(): RestApiBundle\Services\OpenApi\Schema\DocBlockReader
    {
        /** @var RestApiBundle\Services\OpenApi\Schema\DocBlockReader $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\OpenApi\Schema\DocBlockReader::class);

        return $result;
    }

    protected function getRequestModelHelper(): RestApiBundle\Services\OpenApi\RequestModelHelper
    {
        /** @var RestApiBundle\Services\OpenApi\RequestModelHelper $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\OpenApi\RequestModelHelper::class);

        return $result;
    }

    protected function getResponseCollector(): RestApiBundle\Services\OpenApi\ResponseCollector
    {
        /** @var RestApiBundle\Services\OpenApi\ResponseCollector $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\OpenApi\ResponseCollector::class);

        return $result;
    }

    protected function getTypeHintSchemaReader(): RestApiBundle\Services\OpenApi\Schema\TypeHintReader
    {
        /** @var RestApiBundle\Services\OpenApi\Schema\TypeHintReader $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\OpenApi\Schema\TypeHintReader::class);

        return $result;
    }
}
