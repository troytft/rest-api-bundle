<?php
declare(strict_types=1);

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
     * @return Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\ResponseModel\Book[]
     */
    public function listAction(Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\RequestModel\BookList $requestModel): array
    {
        return [];
    }

    /**
     * @OpenApi\Endpoint("Upload book", tags="books")
     *
     * @Route(methods="POST")
     */
    public function uploadAction(Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\RequestModel\UploadBook $requestModel): void
    {
    }
}
