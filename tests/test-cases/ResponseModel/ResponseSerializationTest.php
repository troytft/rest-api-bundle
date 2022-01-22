<?php

use Symfony\Component\HttpFoundation\Request;

class ResponseSerializationTest extends Tests\BaseTestCase
{
    public function testResponseWithNull()
    {
        $request = Request::create('http://localhost/response-model-test/null', 'GET');
        $response = $this->getKernel()->handle($request);

        $this->assertSame(204, $response->getStatusCode());
        $this->assertSame('', $response->getContent());
    }

    public function testResponseWithSingleResponseModel()
    {
        $request = Request::create('http://localhost/response-model-test/single-response-model', 'GET');
        $response = $this->getKernel()->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertMatchesJsonSnapshot($response->getContent());
    }

    public function testResponseWithCollectionOfResponseModels()
    {
        $request = Request::create('http://localhost/response-model-test/collection-of-response-models', 'GET');
        $response = $this->getKernel()->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertMatchesJsonSnapshot($response->getContent());
    }

    public function testResponseWithResponseClass()
    {
        $request = Request::create('http://localhost/response-model-test/response-class', 'GET');
        $response = $this->getKernel()->handle($request);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertMatchesJsonSnapshot($response->getContent());
    }
}
