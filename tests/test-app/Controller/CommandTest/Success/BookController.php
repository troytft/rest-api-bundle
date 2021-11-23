<?php

namespace TestApp\Controller\CommandTest\Success;

use TestApp;
use RestApiBundle\Mapping\OpenApi;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/books')]
class BookController
{
    #[OpenApi\Endpoint('Books list', tags: 'books')]
    #[Route(methods: 'GET')]
    /**
     * @return TestApp\ResponseModel\Book[]
     */
    public function listAction(TestApp\RequestModel\BookList $requestModel): array
    {
        return [];
    }
}
