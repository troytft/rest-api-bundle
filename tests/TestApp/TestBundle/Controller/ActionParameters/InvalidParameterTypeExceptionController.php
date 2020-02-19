<?php

namespace Tests\TestApp\TestBundle\Controller\ActionParameters;

use Tests;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Annotation\Docs;

/**
 * @Route("/action-parameters/invalid-parameter-type-exception")
 */
class InvalidParameterTypeExceptionController extends BaseController
{
    /**
     * @Docs\Endpoint(title="Title", tags={"tag"})
     *
     * @Route(methods="GET")
     *
     * @param Tests\TestApp\TestBundle\Entity\Genre[] $genres
     */
    public function testAction(array $genres)
    {
        return null;
    }
}
