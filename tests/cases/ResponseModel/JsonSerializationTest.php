<?php

namespace cases\ResponseModel;

use RestApiBundle;
use Tests;

class JsonSerializationTest extends Tests\BaseTestCase
{
    public function testDateType(): void
    {
        $this->assertMatchesJsonSnapshot($this->getResponseModelSerializer()->serialize(new Tests\Fixture\ResponseModel\DateType()));
    }

    public function testDateTimeType(): void
    {
        $this->assertMatchesJsonSnapshot($this->getResponseModelSerializer()->serialize(new Tests\Fixture\ResponseModel\DateTimeType()));
    }

    public function testPolyfillEnumType(): void
    {
        $this->assertMatchesJsonSnapshot($this->getResponseModelSerializer()->serialize(new Tests\Fixture\ResponseModel\PolyfillEnumType()));
    }

    public function testPhpEnumType(): void
    {
        $this->assertMatchesJsonSnapshot($this->getResponseModelSerializer()->serialize(new Tests\Fixture\ResponseModel\PhpEnumType()));
    }

    private function getResponseModelSerializer(): RestApiBundle\Services\ResponseModel\Serializer
    {
        return $this->getContainer()->get(RestApiBundle\Services\ResponseModel\Serializer::class);
    }

    private function getResponseModelResolver(): RestApiBundle\Services\OpenApi\ResponseModelResolver
    {
        return $this->getContainer()->get(RestApiBundle\Services\OpenApi\ResponseModelResolver::class);
    }
}


