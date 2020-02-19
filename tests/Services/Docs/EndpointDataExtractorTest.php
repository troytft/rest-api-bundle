<?php

namespace Tests\Services\Docs;

use Tests;
use RestApiBundle;

class EndpointDataExtractorTest extends Tests\BaseBundleTestCase
{
    public function testRouteRequirementsParameterNotPresentedInRoutePath()
    {
        $routes = $this->getRouteFinder()->find(Tests\TestApp\TestBundle\Controller\InvalidDefinition\EmptyRouteRequirementsController::class);

        $this->assertCount(1, $routes);

        try {
            $this->getEndpointDataExtractor()->extractFromRoute($routes[0]);
            $this->fail();
        } catch (RestApiBundle\Exception\Docs\InvalidDefinitionException $exception) {
            $this->assertInstanceOf(RestApiBundle\Exception\Docs\InvalidDefinition\InvalidRouteRequirementsException::class, $exception->getPrevious());
        }
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
