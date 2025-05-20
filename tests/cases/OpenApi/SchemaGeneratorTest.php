<?php

declare(strict_types=1);

class SchemaGeneratorTest extends Tests\BaseTestCase
{
    public function testRedirectResponse(): void
    {
        $endpointData = new RestApiBundle\Model\OpenApi\EndpointData(
            new ReflectionMethod(Tests\Fixture\OpenApi\SchemaGeneratorTest\DefaultController::class, 'redirectResponseAction'),
            new RestApiBundle\Mapping\OpenApi\Endpoint(title: 'title', tags: 'tag'),
            new Symfony\Component\Routing\Annotation\Route('/', methods: 'GET')
        );
        $schema = $this->getSchemaGenerator()->generate([$endpointData]);

        $this->assertMatchesOpenApiSchemaSnapshot($schema);
    }

    public function testBinaryFileResponse(): void
    {
        $endpointData = new RestApiBundle\Model\OpenApi\EndpointData(
            new ReflectionMethod(Tests\Fixture\OpenApi\SchemaGeneratorTest\DefaultController::class, 'binaryFileResponseAction'),
            new RestApiBundle\Mapping\OpenApi\Endpoint(title: 'title', tags: 'tag'),
            new Symfony\Component\Routing\Annotation\Route('/', methods: 'GET')
        );
        $schema = $this->getSchemaGenerator()->generate([$endpointData]);

        $this->assertMatchesOpenApiSchemaSnapshot($schema);
    }

    public function testVoidResponse(): void
    {
        $endpointData = new RestApiBundle\Model\OpenApi\EndpointData(
            new ReflectionMethod(Tests\Fixture\OpenApi\SchemaGeneratorTest\DefaultController::class, 'voidResponseAction'),
            new RestApiBundle\Mapping\OpenApi\Endpoint(title: 'title', tags: 'tag'),
            new Symfony\Component\Routing\Annotation\Route('/', methods: 'GET')
        );
        $schema = $this->getSchemaGenerator()->generate([$endpointData]);

        $this->assertMatchesOpenApiSchemaSnapshot($schema);
    }

    private function getSchemaGenerator(): RestApiBundle\Services\OpenApi\SchemaGenerator
    {
        return $this->getContainer()->get(RestApiBundle\Services\OpenApi\SchemaGenerator::class);
    }
}
