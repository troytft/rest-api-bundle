<?php

namespace Tests\TestCase;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use RestApiBundle;
use Symfony\Component\Routing\Route;
use Tests\TestApp;
use Symfony\Component\HttpKernel\KernelInterface;
use function explode;

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

    protected function getRouteByControllerAndAction(string $controller, string $action): Route
    {
        $routes = $this->getRouteFinder()->find($controller);

        $result = null;
        foreach ($routes as $route) {
            if (explode('::', $route->getDefault('_controller'))[1] === $action) {
                $result = $route;

                break;
            }
        }

        if (!$result) {
            throw new \InvalidArgumentException('Route not found.');
        }

        return $result;
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

    protected function getDocBlockSchemaReader(): RestApiBundle\Services\Docs\Schema\DocBlockSchemaReader
    {
        /** @var RestApiBundle\Services\Docs\Schema\DocBlockSchemaReader $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\Schema\DocBlockSchemaReader::class);

        return $result;
    }

    protected function getResponseModelSchemaReader(): RestApiBundle\Services\Docs\Schema\ResponseModelSchemaReader
    {
        /** @var RestApiBundle\Services\Docs\Schema\ResponseModelSchemaReader $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\Schema\ResponseModelSchemaReader::class);

        return $result;
    }

    protected function getTypeHintSchemaReader(): RestApiBundle\Services\Docs\Schema\TypeHintSchemaReader
    {
        /** @var RestApiBundle\Services\Docs\Schema\TypeHintSchemaReader $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\Schema\TypeHintSchemaReader::class);

        return $result;
    }
}
