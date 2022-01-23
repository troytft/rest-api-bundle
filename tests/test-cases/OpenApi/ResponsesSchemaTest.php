<?php

class ResponsesSchemaTest extends Tests\BaseTestCase
{
    public function testRedirectResponse(): void
    {
        $endpointData = new \RestApiBundle\Model\OpenApi\EndpointData(
            new \ReflectionMethod(\Tests\Fixture\OpenApi\AllResponsesController::class, 'redirectResponseAction'),
            new \RestApiBundle\Mapping\OpenApi\Endpoint(title: 'title', tags: 'tag'),
            new \Symfony\Component\Routing\Annotation\Route('/', methods: 'GET')
        );
        $schema = $this->getSchemaGenerator()->generate([$endpointData]);

        $this->assertMatchesJsonSnapshot($this->convertOpenApiToJson($schema));
    }

    public function testBinaryFileResponse(): void
    {
        $endpointData = new \RestApiBundle\Model\OpenApi\EndpointData(
            new \ReflectionMethod(\Tests\Fixture\OpenApi\AllResponsesController::class, 'binaryFileResponseAction'),
            new \RestApiBundle\Mapping\OpenApi\Endpoint(title: 'title', tags: 'tag'),
            new \Symfony\Component\Routing\Annotation\Route('/', methods: 'GET')
        );
        $schema = $this->getSchemaGenerator()->generate([$endpointData]);

        $this->assertMatchesJsonSnapshot($this->convertOpenApiToJson($schema));
    }

    public function testVoidResponse(): void
    {
        $endpointData = new \RestApiBundle\Model\OpenApi\EndpointData(
            new \ReflectionMethod(\Tests\Fixture\OpenApi\AllResponsesController::class, 'voidResponseAction'),
            new \RestApiBundle\Mapping\OpenApi\Endpoint(title: 'title', tags: 'tag'),
            new \Symfony\Component\Routing\Annotation\Route('/', methods: 'GET')
        );
        $schema = $this->getSchemaGenerator()->generate([$endpointData]);

        $this->assertMatchesJsonSnapshot($this->convertOpenApiToJson($schema));
    }

    private function getSchemaGenerator(): RestApiBundle\Services\OpenApi\SchemaGenerator
    {
        return $this->getContainer()->get(RestApiBundle\Services\OpenApi\SchemaGenerator::class);
    }
}
