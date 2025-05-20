<?php

declare(strict_types=1);

namespace Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess;

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
     * @return ResponseModel\Book[]
     */
    public function listAction(RequestModel\BookList $requestModel): array
    {
        return [];
    }

    /**
     * @OpenApi\Endpoint("Upload book", tags="books")
     *
     * @Route(methods="POST")
     */
    public function uploadAction(RequestModel\UploadBook $requestModel): void
    {
    }
}
