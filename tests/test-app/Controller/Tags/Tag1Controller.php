<?php

namespace TestApp\Controller\Tags;

use TestApp;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Annotation\Docs;

/**
 * @Route("/tag1")
 */
class Tag1Controller
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
     * @Docs\Endpoint(title="Book response model details", tags={"tag1"})
     *
     * @Route(methods="GET")
     */
    public function getGenreAction(): TestApp\ResponseModel\Book
    {
        $book = $this->bookRepository->find(1);

        return new TestApp\ResponseModel\Book($book);
    }
}
