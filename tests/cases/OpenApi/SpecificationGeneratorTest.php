<?php

class SpecificationGeneratorTest extends Tests\BaseTestCase
{
    public function testRedirectResponse(): void
    {
        $endpointData = new \RestApiBundle\Model\OpenApi\EndpointData(
            new \ReflectionMethod(\Tests\Fixture\OpenApi\AllResponsesController::class, 'redirectResponseAction'),
            new \RestApiBundle\Mapping\OpenApi\Endpoint(title: 'title', tags: 'tag'),
            new \Symfony\Component\Routing\Annotation\Route('/', methods: 'GET')
        );
        $specification = $this->getSpecificationGenerator()->generate([$endpointData]);

        $this->assertMatchesJsonSnapshot(json_encode($specification->getSerializableData()));
    }

    public function testBinaryFileResponse(): void
    {
        $endpointData = new \RestApiBundle\Model\OpenApi\EndpointData(
            new \ReflectionMethod(\Tests\Fixture\OpenApi\AllResponsesController::class, 'binaryFileResponseAction'),
            new \RestApiBundle\Mapping\OpenApi\Endpoint(title: 'title', tags: 'tag'),
            new \Symfony\Component\Routing\Annotation\Route('/', methods: 'GET')
        );
        $specification = $this->getSpecificationGenerator()->generate([$endpointData]);

        $this->assertMatchesJsonSnapshot(json_encode($specification->getSerializableData()));
    }

    public function testVoidResponse(): void
    {
        $endpointData = new \RestApiBundle\Model\OpenApi\EndpointData(
            new \ReflectionMethod(\Tests\Fixture\OpenApi\AllResponsesController::class, 'voidResponseAction'),
            new \RestApiBundle\Mapping\OpenApi\Endpoint(title: 'title', tags: 'tag'),
            new \Symfony\Component\Routing\Annotation\Route('/', methods: 'GET')
        );
        $specification = $this->getSpecificationGenerator()->generate([$endpointData]);

        $this->assertMatchesJsonSnapshot(json_encode($specification->getSerializableData()));
    }

    private function getSpecificationGenerator(): RestApiBundle\Services\OpenApi\Specification\SpecificationGenerator
    {
        return $this->getContainer()->get(RestApiBundle\Services\OpenApi\Specification\SpecificationGenerator::class);
    }
}
