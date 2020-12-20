<?php

namespace Tests\TestCase\Services\OpenApi;

use Tests;
use RestApiBundle;
use Symfony;

class EndpointFinderTest extends Tests\TestCase\BaseTestCase
{
    public function testPathParametersNotMatchRouteRequirementsException()
    {
        $controller = Tests\TestApp\TestBundle\Controller\PathParameters\EmptyRouteRequirementsController::class;

        try {
            $this->invokePrivateMethod($this->getEndpointFinder(), 'extractFromController', [$controller]);
            $this->fail();
        } catch (RestApiBundle\Exception\Docs\InvalidDefinitionException $exception) {
            $this->assertInstanceOf(RestApiBundle\Exception\Docs\InvalidDefinition\NotMatchedRoutePlaceholderParameterException::class, $exception->getPrevious());
        }
    }

    public function testAllowedScalarPathParameters()
    {
        $controller = Tests\TestApp\TestBundle\Controller\PathParameters\AllowedScalarParametersController::class;
        $result = $this->invokePrivateMethod($this->getEndpointFinder(), 'extractFromController', [$controller]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey(0, $result);

        $endpointData = $result[0];

        $this->assertCount(2, $endpointData->getPathParameters());

        $this->assertSame('int', $endpointData->getPathParameters()[0]->getName());
        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\IntegerType::class, $endpointData->getPathParameters()[0]->getSchema());
        $this->assertFalse($endpointData->getPathParameters()[0]->getSchema()->getNullable());

        $this->assertSame('string', $endpointData->getPathParameters()[1]->getName());
        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\StringType::class, $endpointData->getPathParameters()[1]->getSchema());
        $this->assertFalse($endpointData->getPathParameters()[1]->getSchema()->getNullable());
    }

    public function testEntityPathParameters()
    {
        $controller = Tests\TestApp\TestBundle\Controller\PathParameters\EntityPathParametersController::class;
        $result = $this->invokePrivateMethod($this->getEndpointFinder(), 'extractFromController', [$controller]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey(0, $result);

        $endpointData = $result[0];

        $this->assertCount(4, $endpointData->getPathParameters());

        $this->assertSame('int', $endpointData->getPathParameters()[0]->getName());
        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\IntegerType::class, $endpointData->getPathParameters()[0]->getSchema());

        $this->assertSame('genre', $endpointData->getPathParameters()[1]->getName());
        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\IntegerType::class, $endpointData->getPathParameters()[1]->getSchema());

        $this->assertSame('string', $endpointData->getPathParameters()[2]->getName());
        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\StringType::class, $endpointData->getPathParameters()[2]->getSchema());

        $this->assertSame('slug', $endpointData->getPathParameters()[3]->getName());
        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\StringType::class, $endpointData->getPathParameters()[3]->getSchema());
    }

    public function testRequestModelForRequest()
    {
        $controller = \Tests\TestApp\TestBundle\Controller\RequestModel\RequestModelForGetRequestController::class;
        $result = $this->invokePrivateMethod($this->getEndpointFinder(), 'extractFromController', [$controller]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey(0, $result);

        /** @var RestApiBundle\DTO\OpenApi\Schema\ClassType $requestModel */
        $requestModel = $result[0]->getRequestModel();

        $this->assertInstanceOf(RestApiBundle\DTO\OpenApi\Schema\ClassType::class, $requestModel);
        $this->assertSame(Tests\TestApp\TestBundle\RequestModel\RequestModelForGetRequest::class, $requestModel->getClass());
        $this->assertFalse($requestModel->getNullable());
    }
}
