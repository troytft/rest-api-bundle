<?php

namespace Tests\Services\Docs;

use Symfony\Component\Routing\Route;
use Tests;
use RestApiBundle;
use function count;

class EndpointDataExtractorTest extends Tests\BaseBundleTestCase
{
    public function testInvalidRouteRequirementsException()
    {
        $route = $this->getSingleRouteFromControllerClass(Tests\TestApp\TestBundle\Controller\InvalidDefinition\EmptyRouteRequirementsController::class);

        try {
            $this->getEndpointDataExtractor()->extractFromRoute($route);
            $this->fail();
        } catch (RestApiBundle\Exception\Docs\InvalidDefinitionException $exception) {
            $this->assertInstanceOf(RestApiBundle\Exception\Docs\InvalidDefinition\InvalidRouteRequirementsException::class, $exception->getPrevious());
        }
    }

    public function testInvalidParameterTypeException()
    {
        $route = $this->getSingleRouteFromControllerClass(Tests\TestApp\TestBundle\Controller\InvalidDefinition\InvalidParameterTypeController::class);

        try {
            $this->getEndpointDataExtractor()->extractFromRoute($route);
            $this->fail();
        } catch (RestApiBundle\Exception\Docs\InvalidDefinitionException $exception) {
            $this->assertInstanceOf(RestApiBundle\Exception\Docs\InvalidDefinition\InvalidParameterTypeException::class, $exception->getPrevious());
        }
    }

    private function getSingleRouteFromControllerClass(string $class): Route
    {
        $routes = $this->getRouteFinder()->find($class);

        if (count($routes) !== 1) {
            throw new \InvalidArgumentException('Controller class contains two or more routes.');
        }

        return $routes[0];
    }

    private function getEndpointDataExtractor(): RestApiBundle\Services\Docs\EndpointDataExtractor
    {
        /** @var RestApiBundle\Services\Docs\EndpointDataExtractor $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\EndpointDataExtractor::class);

        return $result;
    }

    private function getRouteFinder(): RestApiBundle\Services\Docs\RouteFinder
    {
        /** @var RestApiBundle\Services\Docs\RouteFinder $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\RouteFinder::class);

        return $result;
    }
}
