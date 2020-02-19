<?php

namespace Tests\TestApp\TestBundle\Controller\InvalidDefinition;

use Tests;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Annotation\Docs;

/**
 * @Route("/invalid-definition/invalid-return-type")
 */
class InvalidReturnTypeController extends BaseController
{
    /**
     * @Docs\Endpoint(title="Genre response model details", tags={"tag1"})
     *
     * @Route(methods="GET")
     */
    public function testAction()
    {
        return null;
    }
}
