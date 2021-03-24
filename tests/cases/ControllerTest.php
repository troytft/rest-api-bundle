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
        $this->assertMatchesJsonSnapshot($response->getContent());
    }
}
