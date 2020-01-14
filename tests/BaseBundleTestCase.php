<?php

namespace Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use RestApiBundle;
use Tests\DemoApp;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class BaseBundleTestCase extends \Nyholm\BundleTest\BaseBundleTestCase
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
        $this->kernel->setRootDir(__DIR__ . '/DemoApp');
        $this->kernel->addBundle(DemoApp\DemoBundle\DemoBundle::class);
        $this->kernel->addBundle(DoctrineBundle::class);
        $this->kernel->addConfigFile(__DIR__ . '/DemoApp/config/config.yaml');

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
}
