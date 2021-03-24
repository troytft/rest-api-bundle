<?php

namespace TestApp\Controller;

use TestApp;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function array_map;

/**
 * @Route("/demo-responses")
 */
class DemoResponseController
{
    /**
     * @var TestApp\Repository\BookRepository
     */
    private $bookRepository;

    public function __construct(TestApp\Repository\BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
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
    public function collectionOfResponseModelsAction()
    {
        $items = $this->bookRepository->findAll();

        return array_map(function ($item) {
            return new TestApp\ResponseModel\Book($item);
        }, $items);
    }

    /**
     * @Route("/response-class", methods="GET")
     */
    public function responseClassAction()
    {
        return new Response('{"id": 7}', 201);
    }
}
