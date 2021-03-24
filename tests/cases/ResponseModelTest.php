<?php

use Symfony\Component\HttpFoundation\Request;

class ResponseModelTest extends Tests\BaseTestCase
{
    public function testResponseWithNull()
    {
        $request = Request::create('http://localhost/demo-responses/null', 'GET');
        $response = $this->getKernel()->handle($request);

        $this->assertSame(204, $response->getStatusCode());
        $this->assertSame('', $response->getContent());
    }

    public function testResponseWithSingleResponseModel()
    {
        $request = Request::create('http://localhost/demo-responses/single-response-model', 'GET');
        $response = $this->getKernel()->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertMatchesJsonSnapshot($response->getContent());
    }

    public function testResponseWithCollectionOfResponseModels()
    {
        $request = Request::create('http://localhost/demo-responses/collection-of-response-models', 'GET');
        $response = $this->getKernel()->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertMatchesJsonSnapshot($response->getContent());
    }

    public function testResponseWithResponseClass()
    {
        $request = Request::create('http://localhost/demo-responses/response-class', 'GET');
        $response = $this->getKernel()->handle($request);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertMatchesJsonSnapshot($response->getContent());
    }
}
