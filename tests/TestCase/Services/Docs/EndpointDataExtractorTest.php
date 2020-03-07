<?php

namespace Tests\TestCase\Services\Docs;

use Tests;
use RestApiBundle;

class EndpointDataExtractorTest extends Tests\TestCase\BaseTestCase
{
    public function testInvalidRouteRequirementsException()
    {
        $route = $this->getRouteByControllerAndAction(Tests\TestApp\TestBundle\Controller\PathParametersTestController::class, 'emptyRouteRequirementsExceptionAction');

        try {
            $this->getEndpointDataExtractor()->extractFromRoute($route);
            $this->fail();
        } catch (RestApiBundle\Exception\Docs\InvalidDefinitionException $exception) {
            $this->assertInstanceOf(RestApiBundle\Exception\Docs\InvalidDefinition\InvalidRouteRequirementsException::class, $exception->getPrevious());
        }
    }

    public function testAllowedScalarPathParameters()
    {
        $route = $this->getRouteByControllerAndAction(Tests\TestApp\TestBundle\Controller\PathParametersTestController::class, 'allowedScalarParametersAction');
        $endpointData = $this->getEndpointDataExtractor()->extractFromRoute($route);

        $this->assertCount(2, $endpointData->getPathParameters());

        $this->assertSame('int', $endpointData->getPathParameters()[0]->getName());
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\IntegerType::class, $endpointData->getPathParameters()[0]->getSchema());
        $this->assertFalse($endpointData->getPathParameters()[0]->getSchema()->getNullable());

        $this->assertSame('string', $endpointData->getPathParameters()[1]->getName());
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $endpointData->getPathParameters()[1]->getSchema());
        $this->assertFalse($endpointData->getPathParameters()[1]->getSchema()->getNullable());
    }
}
