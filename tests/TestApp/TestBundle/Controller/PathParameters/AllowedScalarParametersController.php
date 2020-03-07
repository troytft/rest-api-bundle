<?php

namespace Tests\TestApp\TestBundle\Controller\PathParameters;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Annotation\Docs;

class AllowedScalarParametersController extends BaseController
{
    /**
     * @Docs\Endpoint(title="Title", tags={"tag"})
     *
     * @Route("/{int}/{string}", methods="GET", requirements={"int": "\d+", "string": "\w+"})
     *
     * @return null
     */
    public function testAction(int $int, string $string)
    {
        return null;
    }
}
