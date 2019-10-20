<?php

namespace Tests;

use RestApiBundle;
use Symfony\Component\HttpFoundation\Request;

class ControllerTest extends BaseBundleTestCase
{
    public function testRequestEmulation()
    {
        $request = Request::create('http://localhost/register', 'POST', ['test' => 'value']);

        try {
            $this->getKernel()->handle($request);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['test' => ['Key is not defined in model.']], $exception->getProperties());
        }
    }
}
