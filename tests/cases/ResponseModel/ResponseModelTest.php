<?php

namespace cases\ResponseModel;

use RestApiBundle;
use Tests;

class ResponseModelTest extends Tests\BaseTestCase
{
    public function testOpenApiSchema(): void
    {
        $this->getResponseModelResolver()->resolveReference(Tests\Fixture\ResponseModel\Root::class);
        $this->assertMatchesOpenApiSchemaSnapshots($this->getResponseModelResolver()->dumpSchemas());
    }

    private function getResponseModelResolver(): RestApiBundle\Services\OpenApi\ResponseModelResolver
    {
        $service = $this->getContainer()->get(RestApiBundle\Services\OpenApi\ResponseModelResolver::class);
        \assert($service instanceof RestApiBundle\Services\OpenApi\ResponseModelResolver);

        return $service;
    }

    public function testJsonSerializer(): void
    {
        $this->assertMatchesJsonSnapshot($this->getResponseModelSerializer()->serialize(new Tests\Fixture\ResponseModel\Root()));
    }

    private function getResponseModelSerializer(): RestApiBundle\Services\ResponseModel\Serializer
    {
        $service = $this->getContainer()->get(RestApiBundle\Services\ResponseModel\Serializer::class);
        \assert($service instanceof RestApiBundle\Services\ResponseModel\Serializer);

        return $service;
    }
}
