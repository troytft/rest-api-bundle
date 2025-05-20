<?php declare(strict_types=1);

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
        return $this->getContainer()->get(RestApiBundle\Services\OpenApi\ResponseModelResolver::class);
    }

    public function testJsonSerializer(): void
    {
        $this->assertMatchesJsonSnapshot($this->getResponseModelSerializer()->serialize(new Tests\Fixture\ResponseModel\Root()));
    }

    private function getResponseModelSerializer(): RestApiBundle\Services\ResponseModel\Serializer
    {
        return $this->getContainer()->get(RestApiBundle\Services\ResponseModel\Serializer::class);
    }
}
