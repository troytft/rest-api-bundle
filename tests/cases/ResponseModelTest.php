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
        $this->assertSame('{"id":1,"title":"Keto Cookbook For Beginners: 1000 Recipes For Quick & Easy Low-Carb Homemade Cooking","author":{"id":0,"name":"","surname":"","birthday":null,"genres":[],"__typename":"Author"},"genre":null,"status":"published","__typename":"Book"}', $response->getContent());
    }

    public function testResponseWithCollectionOfResponseModels()
    {
        $request = Request::create('http://localhost/demo-responses/collection-of-response-models', 'GET');
        $response = $this->getKernel()->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('[{"id":1,"title":"Keto Cookbook For Beginners: 1000 Recipes For Quick & Easy Low-Carb Homemade Cooking","author":{"id":0,"name":"","surname":"","birthday":null,"genres":[],"__typename":"Author"},"genre":null,"status":"published","__typename":"Book"},{"id":2,"title":"Home Stories: Design Ideas for Making a House a Home","author":{"id":0,"name":"","surname":"","birthday":null,"genres":[],"__typename":"Author"},"genre":null,"status":"published","__typename":"Book"}]', $response->getContent());
    }

    public function testResponseWithResponseClass()
    {
        $request = Request::create('http://localhost/demo-responses/response-class', 'GET');
        $response = $this->getKernel()->handle($request);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame('{"id": 7}', $response->getContent());
    }
}
