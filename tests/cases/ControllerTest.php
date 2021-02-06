<?php

use Symfony\Component\HttpFoundation\Request;

class ControllerTest extends Tests\BaseTestCase
{
    public function testRequestEmulation()
    {
        $jsonBody = '{"test": "value"}';
        $request = Request::create('http://localhost/register', 'POST', [], [], [], [], $jsonBody);
        $response = $this->getKernel()->handle($request);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('{"properties":{"test":["The key is not defined in the model."],"stringField":["This value should not be null."],"modelField":["This value should not be null."],"collectionField":["This value should not be null."],"collectionOfIntegers":["This value should not be null."]}}', $response->getContent());
    }
}
