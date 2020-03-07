<?php

namespace Tests\TestCase\Services\Docs;

use Tests;
use RestApiBundle;

class EndpointDataExtractorTest extends Tests\TestCase\BaseTestCase
{
    public function testPathParametersNotMatchRouteRequirementsException()
    {
        $route = $this->getOneRouteFromControllerClass(Tests\TestApp\TestBundle\Controller\PathParameters\EmptyRouteRequirementsController::class);

        try {
            $this->getEndpointDataExtractor()->extractFromRoute($route);
            $this->fail();
        } catch (RestApiBundle\Exception\Docs\InvalidDefinitionException $exception) {
            $this->assertInstanceOf(RestApiBundle\Exception\Docs\InvalidDefinition\PathParametersNotMatchRouteRequirementsException::class, $exception->getPrevious());
        }
    }

    public function testAllowedScalarPathParameters()
    {
        $route = $this->getOneRouteFromControllerClass(Tests\TestApp\TestBundle\Controller\PathParameters\AllowedScalarParametersController::class);
        $endpointData = $this->getEndpointDataExtractor()->extractFromRoute($route);

        $this->assertCount(2, $endpointData->getPathParameters());

        $this->assertSame('int', $endpointData->getPathParameters()[0]->getName());
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\IntegerType::class, $endpointData->getPathParameters()[0]->getSchema());
        $this->assertFalse($endpointData->getPathParameters()[0]->getSchema()->getNullable());

        $this->assertSame('string', $endpointData->getPathParameters()[1]->getName());
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $endpointData->getPathParameters()[1]->getSchema());
        $this->assertFalse($endpointData->getPathParameters()[1]->getSchema()->getNullable());
    }

    public function testNotAllowedFunctionParameterTypeParameter()
    {
        $route = $this->getOneRouteFromControllerClass(Tests\TestApp\TestBundle\Controller\PathParameters\NotAllowedFunctionParameterTypeController::class);

        try {
            $this->getEndpointDataExtractor()->extractFromRoute($route);
            $this->fail();
        } catch (RestApiBundle\Exception\Docs\InvalidDefinitionException $exception) {
            $this->assertInstanceOf(RestApiBundle\Exception\Docs\InvalidDefinition\NotAllowedFunctionParameterTypeException::class, $exception->getPrevious());
        }
    }
}
