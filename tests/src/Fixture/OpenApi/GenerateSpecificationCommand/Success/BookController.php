<?php

namespace Tests\Fixture\OpenApi\GenerateSpecificationCommand\Success;

use Tests;
use RestApiBundle\Mapping\OpenApi;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/books")
 */
class BookController
{
    /**
     * @OpenApi\Endpoint("Books list", tags="books")
     *
     * @Route(methods="GET")
     *
     * @return Tests\Fixture\OpenApi\GenerateSpecificationCommand\Success\ResponseModel\Book[]
     */
    public function listAction(Tests\Fixture\OpenApi\GenerateSpecificationCommand\Success\RequestModel\BookList $requestModel): array
    {
        return [];
    }
}
