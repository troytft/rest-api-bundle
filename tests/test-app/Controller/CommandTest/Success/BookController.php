<?php

namespace TestApp\Controller\CommandTest\Success;

use Symfony\Component\HttpFoundation\RedirectResponse;
use TestApp;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Mapping\OpenApi as Docs;

/**
 * @Route("/books")
 */
class BookController
{
    #[Docs\Endpoint('Books list', tags: 'books')]
    /**
     * @Docs\Endpoint("Books list", tags="books")
     *
     * @Route(methods="GET")
     *
     * @return TestApp\ResponseModel\Book[]
     */
    public function listAction(TestApp\RequestModel\BookList $requestModel)
    {
        return [];
    }

    #[Docs\Endpoint('Response with redirect', tags: 'books')]
    #[Route('/test-redirect', methods: 'GET')]
    public function testRedirectAction(): RedirectResponse
    {
        return new RedirectResponse('');
    }
}
