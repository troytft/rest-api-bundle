<?php

namespace Tests\TestCase;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use RestApiBundle;
use Symfony\Component\Routing\Route;
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

    protected function getOneRouteFromControllerClass(string $class): Route
    {
        $routes = $this->getRouteFinder()->find($class);

        if (!$routes) {
            throw new \InvalidArgumentException('Route not found.');
        }

        if (count($routes) > 1) {
            throw new \InvalidArgumentException('Found two or more routes.');
        }

        return $routes[0];
    }



    protected function invokePrivateMethod($object, string $methodName, array $parameters = [])
    {
        $reflection = RestApiBundle\Services\ReflectionClassStore::get(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    protected function getEndpointDataExtractor(): RestApiBundle\Services\Docs\EndpointDataExtractor
    {
        /** @var RestApiBundle\Services\Docs\EndpointDataExtractor $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\EndpointDataExtractor::class);

        return $result;
    }

    protected function getRouteFinder(): RestApiBundle\Services\Docs\RouteFinder
    {
        /** @var RestApiBundle\Services\Docs\RouteFinder $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\RouteFinder::class);

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

    protected function getOpenApiSpecificationGenerator(): RestApiBundle\Services\Docs\OpenApiSpecificationGenerator
    {
        /** @var RestApiBundle\Services\Docs\OpenApiSpecificationGenerator $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\OpenApiSpecificationGenerator::class);

        return $result;
    }
}
