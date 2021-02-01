<?php

class EndpointFinderTest extends Tests\BaseTestCase
{
    public function testPathParametersNotMatchRouteRequirementsException()
    {
        $controller = TestApp\Controller\PathParameters\EmptyRouteRequirementsController::class;

        try {
            $this->invokePrivateMethod($this->getEndpointFinder(), 'extractFromController', [$controller]);
            $this->fail();
        } catch (RestApiBundle\Exception\Docs\InvalidDefinitionException $exception) {
            $this->assertInstanceOf(RestApiBundle\Exception\Docs\InvalidDefinition\NotMatchedRoutePlaceholderParameterException::class, $exception->getPrevious());
        }
    }

    public function testAllowedScalarPathParameters()
    {
        $controller = TestApp\Controller\PathParameters\AllowedScalarParametersController::class;
        $result = $this->invokePrivateMethod($this->getEndpointFinder(), 'extractFromController', [$controller]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey(0, $result);

        $endpointData = $result[0];

        $this->assertCount(2, $endpointData->getPathParameters());

        $this->assertSame('int', $endpointData->getPathParameters()[0]->getName());
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Types\IntegerType::class, $endpointData->getPathParameters()[0]->getSchema());
        $this->assertFalse($endpointData->getPathParameters()[0]->getSchema()->getNullable());

        $this->assertSame('string', $endpointData->getPathParameters()[1]->getName());
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Types\StringType::class, $endpointData->getPathParameters()[1]->getSchema());
        $this->assertFalse($endpointData->getPathParameters()[1]->getSchema()->getNullable());
    }

    public function testEntityPathParameters()
    {
        $controller = TestApp\Controller\PathParameters\EntityPathParametersController::class;
        $result = $this->invokePrivateMethod($this->getEndpointFinder(), 'extractFromController', [$controller]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey(0, $result);

        $endpointData = $result[0];

        $this->assertCount(4, $endpointData->getPathParameters());

        $this->assertSame('int', $endpointData->getPathParameters()[0]->getName());
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Types\IntegerType::class, $endpointData->getPathParameters()[0]->getSchema());

        $this->assertSame('genre', $endpointData->getPathParameters()[1]->getName());
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Types\IntegerType::class, $endpointData->getPathParameters()[1]->getSchema());

        $this->assertSame('string', $endpointData->getPathParameters()[2]->getName());
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Types\StringType::class, $endpointData->getPathParameters()[2]->getSchema());

        $this->assertSame('slug', $endpointData->getPathParameters()[3]->getName());
        $this->assertInstanceOf(RestApiBundle\DTO\Docs\Types\StringType::class, $endpointData->getPathParameters()[3]->getSchema());
    }
}
