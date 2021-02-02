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

    public function notNullableResponseModelTypeHintAction(): TestApp\ResponseModel\Genre
    {
        return $this->getGenreResponseModel(1, 'test-genre');
    }

    public function nullableResponseModelTypeHintAction(): ?TestApp\ResponseModel\Genre
    {
        return $this->getGenreResponseModel(1, 'test-genre');
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
     * @return TestApp\ResponseModel\Genre
     */
    public function methodWithSingleResponseModelReturnTag()
    {
        return $this->getGenreResponseModel(1, 'test-genre');
    }

    /**
     * @return TestApp\ResponseModel\Genre|null
     */
    public function methodWithNullableSingleResponseModelReturnTag()
    {
        return $this->getGenreResponseModel(1, 'test-genre');
    }

    /**
     * @return TestApp\ResponseModel\Genre[]
     */
    public function methodWithArrayOfResponseModelsReturnTag()
    {
        return [$this->getGenreResponseModel(1, 'test-genre')];
    }

    /**
     * @return TestApp\ResponseModel\Genre[]|null
     */
    public function methodWithNullableArrayOfResponseModelsReturnTag()
    {
        return [$this->getGenreResponseModel(1, 'test-genre')];
    }

    /**
     * @Docs\Endpoint(title="Genre response model details", tags={"demo"})
     *
     * @Route("/genres/by-slug/{slug}", methods="GET", requirements={"slug": "[\w-]+"})
     *
     * @return TestApp\ResponseModel\Genre
     */
    public function detailsBySlugAction(string $slug)
    {
        return $this->getGenreResponseModel(1, $slug);
    }

    private function getGenreResponseModel(int $id, string $slug): TestApp\ResponseModel\Genre
    {
        $entity = new TestApp\Entity\Genre();
        $entity
            ->setId($id)
            ->setSlug($slug);

        return new TestApp\ResponseModel\Genre($entity);
    }
}
