<?php

namespace Tests\Docs;

use Tests;
use RestApiBundle;

class RouteDataExtractorTest extends Tests\BaseBundleTestCase
{
    public function testControllerClassFilter()
    {
        $this->assertCount(2, $this->getRouteDataExtractor()->getItems('Tests\DemoApp\DemoBundle\Controller\Tags'));
        $this->assertCount(1, $this->getRouteDataExtractor()->getItems('Tests\DemoApp\DemoBundle\Controller\Tags\Tag1'));
    }

    private function getRouteDataExtractor(): RestApiBundle\Services\Docs\RouteDataExtractor
    {
        /** @var RestApiBundle\Services\Docs\RouteDataExtractor $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\RouteDataExtractor::class);

        return $result;
    }
}
