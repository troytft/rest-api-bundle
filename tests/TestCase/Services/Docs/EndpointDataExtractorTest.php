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
            $this->assertInstanceOf(RestApiBundle\Exception\Docs\InvalidDefinition\NotMatchedRoutePlaceholderParameterException::class, $exception->getPrevious());
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

    public function testEntityPathParameters()
    {
        $route = $this->getOneRouteFromControllerClass(Tests\TestApp\TestBundle\Controller\PathParameters\EntityPathParametersController::class);
        $endpointData = $this->getEndpointDataExtractor()->extractFromRoute($route);

        $this->assertCount(4, $endpointData->getPathParameters());

        $this->assertSame('int', $endpointData->getPathParameters()[0]->getName());
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\IntegerType::class, $endpointData->getPathParameters()[0]->getSchema());

        $this->assertSame('genre', $endpointData->getPathParameters()[1]->getName());
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\IntegerType::class, $endpointData->getPathParameters()[1]->getSchema());

        $this->assertSame('string', $endpointData->getPathParameters()[2]->getName());
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $endpointData->getPathParameters()[2]->getSchema());

        $this->assertSame('slug', $endpointData->getPathParameters()[3]->getName());
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $endpointData->getPathParameters()[3]->getSchema());
    }

    public function testRequestModelForGetRequest()
    {
        $route = $this->getOneRouteFromControllerClass(\Tests\TestApp\TestBundle\Controller\RequestModel\RequestModelForGetRequestController::class);
        $endpointData = $this->getEndpointDataExtractor()->extractFromRoute($route);

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\ObjectType::class, $endpointData->getRequestModel());
        $this->assertCount(2, $endpointData->getRequestModel()->getProperties());

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\IntegerType::class, $endpointData->getRequestModel()->getProperties()['offset']);
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\IntegerType::class, $endpointData->getRequestModel()->getProperties()['limit']);
    }
}
