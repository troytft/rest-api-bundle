<?php

namespace Tests\TestApp\TestBundle\Controller\PathParameters;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Annotation\Docs;

class BoolParameterController extends BaseController
{
    /**
     * @Docs\Endpoint(title="Title", tags={"tag"})
     *
     * @Route("/{bool}", methods="GET", requirements={"bool": ".*"})
     *
     * @return null
     */
    public function testAction(bool $bool)
    {
        return null;
    }
}
