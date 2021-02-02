<?php

namespace TestApp\Controller\CommandTest\Success;

use TestApp;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Annotation\Docs;

/**
 * @Route("/books")
 */
class BookController
{
    /**
     * @Docs\Endpoint(title="Books list", tags={"books"})
     *
     * @Route(methods="GET")
     *
     * @return TestApp\ResponseModel\Book[]
     */
    public function listAction()
    {
        return [];
    }
}
