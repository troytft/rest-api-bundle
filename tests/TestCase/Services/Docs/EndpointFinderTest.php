<?php

namespace Tests\TestCase\Services\Docs;

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
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\IntegerType::class, $endpointData->getPathParameters()[0]->getSchema());
        $this->assertFalse($endpointData->getPathParameters()[0]->getSchema()->getNullable());

        $this->assertSame('string', $endpointData->getPathParameters()[1]->getName());
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $endpointData->getPathParameters()[1]->getSchema());
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
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\IntegerType::class, $endpointData->getPathParameters()[0]->getSchema());

        $this->assertSame('genre', $endpointData->getPathParameters()[1]->getName());
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\IntegerType::class, $endpointData->getPathParameters()[1]->getSchema());

        $this->assertSame('string', $endpointData->getPathParameters()[2]->getName());
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $endpointData->getPathParameters()[2]->getSchema());

        $this->assertSame('slug', $endpointData->getPathParameters()[3]->getName());
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\StringType::class, $endpointData->getPathParameters()[3]->getSchema());
    }

    public function testRequestModelForRequest()
    {
        $controller = \Tests\TestApp\TestBundle\Controller\RequestModel\RequestModelForGetRequestController::class;
        $result = $this->invokePrivateMethod($this->getEndpointFinder(), 'extractFromController', [$controller]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey(0, $result);

        $endpointData = $result[0];

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\ObjectType::class, $endpointData->getRequestModel());
        $this->assertCount(2, $endpointData->getRequestModel()->getProperties());

        $this->assertArrayHasKey('offset', $endpointData->getRequestModel()->getProperties());

        /** @var RestApiBundle\DTO\Docs\Schema\IntegerType $offset */
        $offset = $endpointData->getRequestModel()->getProperties()['offset'];

        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\IntegerType::class, $offset);
        $this->assertCount(2, $offset->getConstraints());
        $this->assertInstanceOf(Symfony\Component\Validator\Constraints\Range::class, $offset->getConstraints()[0]);
        $this->assertInstanceOf(Symfony\Component\Validator\Constraints\NotNull::class, $offset->getConstraints()[1]);

        $this->assertArrayHasKey('limit', $endpointData->getRequestModel()->getProperties());

        /** @var RestApiBundle\DTO\Docs\Schema\IntegerType $limit */
        $limit = $endpointData->getRequestModel()->getProperties()['limit'];
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Schema\IntegerType::class, $limit);

        $this->assertCount(2, $limit->getConstraints());
        $this->assertInstanceOf(Symfony\Component\Validator\Constraints\Range::class, $limit->getConstraints()[0]);
        $this->assertInstanceOf(Symfony\Component\Validator\Constraints\NotNull::class, $limit->getConstraints()[1]);
    }
}
