<?php

namespace TestApp\Controller;

use TestApp;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Mapping\OpenApi;
use Symfony\Component\HttpFoundation\Response;

use function array_map;

class DemoController
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
     * @Route("/register", methods="POST")
     */
    public function registerAction(TestApp\RequestModel\ModelWithValidation $model): Response
    {
        if ($model->getStringField() === '') {
            throw new \InvalidArgumentException();
        }

        return new Response('ok');
    }

    public function methodWithEmptyTypeHintAction()
    {
    }

    public function notNullableResponseModelTypeHintAction(): TestApp\ResponseModel\Book
    {
        $book = $this->bookRepository->find(1);

        return new TestApp\ResponseModel\Book($book);
    }

    public function nullableResponseModelTypeHintAction(): ?TestApp\ResponseModel\Book
    {
        return null;
    }

    public function voidReturnTypeAction(): void
    {
    }

    /**
     *
     */
    public function methodWithoutReturnTag()
    {
    }

    /**
     * @return null
     */
    public function methodWithNullReturnTag()
    {
        return null;
    }

    /**
     * @return TestApp\ResponseModel\Book
     */
    public function methodWithSingleResponseModelReturnTag()
    {
        $book = $this->bookRepository->find(1);

        return new TestApp\ResponseModel\Book($book);
    }

    /**
     * @return TestApp\ResponseModel\Book|null
     */
    public function methodWithNullableSingleResponseModelReturnTag()
    {
        return null;
    }

    /**
     * @return TestApp\ResponseModel\Book[]
     */
    public function methodWithArrayOfResponseModelsReturnTag()
    {
        $items = $this->bookRepository->findAll();

        return array_map(function ($item) {
            return new TestApp\ResponseModel\Book($item);
        }, $items);
    }

    /**
     * @return TestApp\ResponseModel\Book[]|null
     */
    public function methodWithNullableArrayOfResponseModelsReturnTag()
    {
        $items = $this->bookRepository->findAll();

        return array_map(function ($item) {
            return new TestApp\ResponseModel\Book($item);
        }, $items);
    }

    /**
     * @OpenApi\Endpoint(title="Get book by slug", tags={"demo"})
     *
     * @Route("/books/by-slug/{slug}", methods="GET", requirements={"slug": "[\w-]+"})
     *
     * @return TestApp\ResponseModel\Book
     */
    public function detailsBySlugAction(string $slug)
    {
        $book = $this->bookRepository->findOneBy(['slug' => $slug]);

        return new TestApp\ResponseModel\Book($book);
    }
}
