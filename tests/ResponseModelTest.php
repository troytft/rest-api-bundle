<?php

namespace Tests;

use Symfony\Component\HttpFoundation\Request;
use Tests;

class ResponseModelTest extends BaseBundleTestCase
{
    public function testSerializer()
    {
        $entity = new Tests\DemoApp\DemoBundle\Entity\Genre();
        $entity
            ->setId(13)
            ->setSlug('genre-slug');

        $json = $this->getResponseSerializer()->toJson(new Tests\DemoApp\DemoBundle\ResponseModel\Genre($entity));

        $this->assertSame('{"id":13,"slug":"genre-slug","__typename":"Genre"}', $json);
    }

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
        $this->assertSame('{"id":1,"slug":"demo-slug","__typename":"Genre"}', $response->getContent());
    }

    public function testResponseWithCollectionOfResponseModels()
    {
        $request = Request::create('http://localhost/demo-responses/collection-of-response-models', 'GET');
        $response = $this->getKernel()->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('[{"id":1,"slug":"1-demo-slug","__typename":"Genre"},{"id":2,"slug":"2-demo-slug","__typename":"Genre"},{"id":3,"slug":"3-demo-slug","__typename":"Genre"}]', $response->getContent());
    }

    public function testResponseWithResponseClass()
    {
        $request = Request::create('http://localhost/demo-responses/response-class', 'GET');
        $response = $this->getKernel()->handle($request);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame('{"id": 7}', $response->getContent());
    }
}
