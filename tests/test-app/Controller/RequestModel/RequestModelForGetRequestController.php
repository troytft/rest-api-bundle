<?php

namespace TestApp\Controller\RequestModel;

use TestApp;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Mapping\OpenApi;

class RequestModelForGetRequestController
{
    /**
     * @OpenApi\Endpoint(title="Title", tags={"tag"})
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
