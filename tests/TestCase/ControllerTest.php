<?php

namespace Tests\TestCase;

use Symfony\Component\HttpFoundation\Request;

class ControllerTest extends BaseBundleTestCase
{
    public function testRequestEmulation()
    {
        $jsonBody = '{"test": "value"}';
        $request = Request::create('http://localhost/register', 'POST', [], [], [], [], $jsonBody);
        $response = $this->getKernel()->handle($request);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('{"properties":{"test":["The key is not defined in the model."]}}', $response->getContent());
    }
}
