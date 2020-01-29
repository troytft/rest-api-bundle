<?php

namespace Tests\Services\Docs;

use Tests;
use RestApiBundle;

class RouteDataExtractorTest extends Tests\BaseBundleTestCase
{
    public function testControllerClassFilter()
    {
        $this->assertCount(2, $this->getRouteDataExtractor()->getItems('Tests\TestApp\TestBundle\Controller\Tags'));
        $this->assertCount(1, $this->getRouteDataExtractor()->getItems('Tests\TestApp\TestBundle\Controller\Tags\Tag1'));
    }

    public function testSameCountOfPathParametersAndPathRequirements()
    {
        try {
            $this->getRouteDataExtractor()->getItems(Tests\TestApp\TestBundle\Controller\InvalidDefinition\EmptyRouteRequirementsController::class);
            $this->fail();
        } catch (RestApiBundle\Exception\Docs\InvalidDefinitionException $exception) {
            $this->assertInstanceOf(RestApiBundle\Exception\Docs\InvalidDefinition\InvalidPathParametersException::class, $exception->getPrevious());
        }
    }

    private function getRouteDataExtractor(): RestApiBundle\Services\Docs\RouteDataExtractor
    {
        /** @var RestApiBundle\Services\Docs\RouteDataExtractor $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Docs\RouteDataExtractor::class);

        return $result;
    }
}
