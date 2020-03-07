<?php

namespace Tests\TestCase\Services\Docs;

use Tests;

class RouteFinderTest extends Tests\TestCase\BaseTestCase
{
    public function testNamespaceFilter()
    {
        $this->assertCount(2, $this->getRouteFinder()->find('Tests\TestApp\TestBundle\Controller\Tags'));
        $this->assertCount(1, $this->getRouteFinder()->find('Tests\TestApp\TestBundle\Controller\Tags\Tag1'));
    }
}
