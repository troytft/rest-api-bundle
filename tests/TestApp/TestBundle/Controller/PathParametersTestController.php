<?php

namespace Tests\TestApp\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Annotation\Docs;

/**
 * @Route("/path-parameters")
 */
class PathParametersTestController extends BaseController
{
    /**
     * @Docs\Endpoint(title="Test endpoint", tags={"tag1"})
     *
     * @Route("/empty-route-requirements-exception/{param}/{param-with-dash}", methods="GET", requirements={"parameter"})
     *
     * @return null
     */
    public function emptyRouteRequirementsExceptionAction()
    {
        return null;
    }

    /**
     * @Docs\Endpoint(title="Test endpoint", tags={"tag1"})
     *
     * @Route("/allowed-scalar-parameters/{int}/{string}", methods="GET", requirements={"int": "\d+", "string": "\w+"})
     *
     * @return null
     */
    public function allowedScalarParametersAction(int $int, string $string)
    {
        return null;
    }
}
