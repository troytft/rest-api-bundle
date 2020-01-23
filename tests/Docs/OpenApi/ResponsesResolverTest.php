<?php

namespace Tests\Docs\OpenApi;

use Tests;
use RestApiBundle;
use cebe\openapi\spec as OpenApi;

class ResponsesResolverTest extends Tests\BaseBundleTestCase
{
    public function testNullType()
    {
        $returnType = new RestApiBundle\DTO\Docs\ReturnType\NullType();
        $responses = $this->getResponsesResolver()->resolve($returnType);

        $this->assertCount(1, $responses);
        $this->assertResponsesContainsNullResponse($responses);
    }

    private function assertResponsesContainsNullResponse(OpenApi\Responses $responses)
    {
        $response = $responses->getResponse('204');

        $this->assertInstanceOf(OpenApi\Response::class, $response);
        $this->assertSame('Success response with empty body', $response->description);
    }

    private function getResponsesResolver(): RestApiBundle\Services\Docs\OpenApi\ResponsesResolver
    {
        $value = $this->getContainer()->get(RestApiBundle\Services\Docs\OpenApi\ResponsesResolver::class);
        if (!$value instanceof RestApiBundle\Services\Docs\OpenApi\ResponsesResolver) {
            throw new \InvalidArgumentException();
        }

        return $value;
    }
}
