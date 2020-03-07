<?php

namespace Tests\TestCase\Services\Docs;

use Tests;
use RestApiBundle;

class RouteFinderTest extends Tests\TestCase\BaseBundleTestCase
{
    public function testNamespaceFilter()
    {
        $this->assertCount(2, $this->getRouteFinder()->find('Tests\TestApp\TestBundle\Controller\Tags'));
        $this->assertCount(1, $this->getRouteFinder()->find('Tests\TestApp\TestBundle\Controller\Tags\Tag1'));
    }

    private function getRouteFinder(): RestApiBundle\Services\Docs\RouteFinder
    {
        /** @var RestApiBundle\Services\Docs\RouteFinder $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\RouteFinder::class);

        return $result;
    }
}
