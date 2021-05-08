<?php

namespace TestApp\Controller\CommandTest\Success;

use TestApp;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Mapping\OpenApi;

/**
 * @Route("/books")
 */
class BookController
{
    /**
     * @OpenApi\Endpoint(title="Books list", tags={"books"})
     *
     * @Route(methods="GET")
     *
     * @return TestApp\ResponseModel\Book[]
     */
    public function listAction(TestApp\RequestModel\BookList $requestModel)
    {
        return [];
    }
}
