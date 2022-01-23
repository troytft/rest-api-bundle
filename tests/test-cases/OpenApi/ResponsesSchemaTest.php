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
        $schema = $this->getSpecificationGenerator()->generate([$endpointData]);

        $this->assertMatchesJsonSnapshot(RestApiBundle\Helper\OpenApiHelper::toJson($schema));
    }

    public function testBinaryFileResponse(): void
    {
        $endpointData = new \RestApiBundle\Model\OpenApi\EndpointData(
            new \ReflectionMethod(\Tests\Fixture\OpenApi\AllResponsesController::class, 'binaryFileResponseAction'),
            new \RestApiBundle\Mapping\OpenApi\Endpoint(title: 'title', tags: 'tag'),
            new \Symfony\Component\Routing\Annotation\Route('/', methods: 'GET')
        );
        $schema = $this->getSpecificationGenerator()->generate([$endpointData]);

        $this->assertMatchesJsonSnapshot(RestApiBundle\Helper\OpenApiHelper::toJson($schema));
    }

    public function testVoidResponse(): void
    {
        $endpointData = new \RestApiBundle\Model\OpenApi\EndpointData(
            new \ReflectionMethod(\Tests\Fixture\OpenApi\AllResponsesController::class, 'voidResponseAction'),
            new \RestApiBundle\Mapping\OpenApi\Endpoint(title: 'title', tags: 'tag'),
            new \Symfony\Component\Routing\Annotation\Route('/', methods: 'GET')
        );
        $schema = $this->getSpecificationGenerator()->generate([$endpointData]);

        $this->assertMatchesJsonSnapshot(RestApiBundle\Helper\OpenApiHelper::toJson($schema));
    }

    private function getSpecificationGenerator(): RestApiBundle\Services\OpenApi\Schema\SchemaGenerator
    {
        return $this->getContainer()->get(RestApiBundle\Services\OpenApi\Schema\SchemaGenerator::class);
    }
}
