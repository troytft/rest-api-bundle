<?php

namespace Tests\TestCase\Services\Docs;

use Tests;
use RestApiBundle;

class EndpointDataExtractorTest extends Tests\TestCase\BaseTestCase
{
    public function testInvalidRouteRequirementsException()
    {
        $route = $this->getRouteByControllerAndAction(Tests\TestApp\TestBundle\Controller\ActionParameters\PathParametersTestController::class, 'emptyRouteRequirementsExceptionAction');

        try {
            $this->getEndpointDataExtractor()->extractFromRoute($route);
            $this->fail();
        } catch (RestApiBundle\Exception\Docs\InvalidDefinitionException $exception) {
            $this->assertInstanceOf(RestApiBundle\Exception\Docs\InvalidDefinition\InvalidRouteRequirementsException::class, $exception->getPrevious());
        }
    }
}
