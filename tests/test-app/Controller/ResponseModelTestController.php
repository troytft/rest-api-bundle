<?php

namespace TestApp\Controller;

use TestApp;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function array_map;

/**
 * @Route("/response-model-test")
 */
class ResponseModelTestController
{
    public function __construct(private TestApp\Repository\BookRepository $bookRepository)
    {
    }

    /**
     * @Route("/null", methods="GET")
     */
    public function nullAction()
    {
        return null;
    }

    /**
     * @Route("/single-response-model", methods="GET")
     */
    public function singeResponseModelAction(): TestApp\ResponseModel\Book
    {
        $book = $this->bookRepository->find(1);

        return new TestApp\ResponseModel\Book($book);
    }

    /**
     * @Route("/collection-of-response-models", methods="GET")
     *
     * @return TestApp\ResponseModel\Book[]
     */
    public function collectionOfResponseModelsAction(): array
    {
        $items = $this->bookRepository->findAll();

        return array_map(fn($item) => new TestApp\ResponseModel\Book($item), $items);
    }

    /**
     * @Route("/response-class", methods="GET")
     */
    public function responseClassAction(): Response
    {
        return new Response('{"id": 7}', 201);
    }
}
