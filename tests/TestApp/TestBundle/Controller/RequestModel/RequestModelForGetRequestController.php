<?php

namespace Tests\TestApp\TestBundle\Controller\RequestModel;

use Tests;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Annotation\Docs;

class RequestModelForGetRequestController extends BaseController
{
    /**
     * @Docs\Endpoint(title="Title", tags={"tag"})
     *
     * @Route(methods="GET")
     *
     * @return null
     */
    public function testAction(Tests\TestApp\TestBundle\RequestModel\RequestModelForGetRequest $requestModel)
    {
        return null;
    }
}
