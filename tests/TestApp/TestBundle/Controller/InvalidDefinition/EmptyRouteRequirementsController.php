<?php

namespace Tests\TestApp\TestBundle\Controller\InvalidDefinition;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Annotation\Docs;

/**
 * @Route("/invalid-definition/empty-route-requirements")
 */
class EmptyRouteRequirementsController extends BaseController
{
    /**
     * @Docs\Endpoint(title="Test endpoint", tags={"tag1"})
     *
     * @Route("/{param}/{param-with-dash}", methods="GET", requirements={"parameter"})
     *
     * @return null
     */
    public function testAction()
    {
        return null;
    }
}
