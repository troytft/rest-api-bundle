<?php

namespace TestApp\Controller;

use Symfony\Component\HttpFoundation\Response;
use TestApp;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Annotation\Docs;

class DemoController
{
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
        return $this->getBookResponseModel(1, 'test-genre');
    }

    public function nullableResponseModelTypeHintAction(): ?TestApp\ResponseModel\Book
    {
        return $this->getBookResponseModel(1, 'test-genre');
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
        return $this->getBookResponseModel(1, 'test-slug');
    }

    /**
     * @return TestApp\ResponseModel\Book|null
     */
    public function methodWithNullableSingleResponseModelReturnTag()
    {
        return $this->getBookResponseModel(1, 'test-slug');
    }

    /**
     * @return TestApp\ResponseModel\Book[]
     */
    public function methodWithArrayOfResponseModelsReturnTag()
    {
        return [$this->getBookResponseModel(1, 'test-slug')];
    }

    /**
     * @return TestApp\ResponseModel\Book[]|null
     */
    public function methodWithNullableArrayOfResponseModelsReturnTag()
    {
        return [$this->getBookResponseModel(1, 'test-slug')];
    }

    /**
     * @Docs\Endpoint(title="Get book by slug", tags={"demo"})
     *
     * @Route("/books/by-slug/{slug}", methods="GET", requirements={"slug": "[\w-]+"})
     *
     * @return TestApp\ResponseModel\Genre
     */
    public function detailsBySlugAction(string $slug)
    {
        return $this->getBookResponseModel(1, $slug);
    }

    private function getBookResponseModel(int $id, string $slug): TestApp\ResponseModel\Book
    {
        $entity = new TestApp\Entity\Book();
        $entity
            ->setId($id)
            ->setSlug($slug);

        return new TestApp\ResponseModel\Genre($entity);
    }
}
