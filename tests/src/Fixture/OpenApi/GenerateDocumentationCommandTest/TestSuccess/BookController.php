<?php

namespace Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess;

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
     * @return Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\ResponseModel\TestDateModel[]
     */
    public function listAction(Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\RequestModel\BookList $requestModel): array
    {
        return [];
    }
}
