<?php

namespace TestApp\Controller\RequestModel;

use TestApp;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Annotation\Docs;

class RequestModelForGetRequestController extends BaseController
{
    /**
     * @Docs\Endpoint(title="Title", tags={"tag"})
     *
     * @Route("/test", methods="GET")
     *
     * @return null
     */
    public function testAction(TestApp\RequestModel\RequestModelForGetRequest $requestModel)
    {
        return null;
    }
}
