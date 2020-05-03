<?php

namespace Tests\TestApp\TestBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Tests;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Annotation\Docs;

class DemoController extends BaseController
{
    /**
     * @Route("/register", methods="POST")
     */
    public function registerAction(Tests\TestApp\TestBundle\RequestModel\ModelWithValidation $model): Response
    {
        if ($model->getStringField() === '') {
            throw new \InvalidArgumentException();
        }

        return new Response('ok');
    }

    public function notNullableResponseModelTypeHintAction(): Tests\TestApp\TestBundle\ResponseModel\Genre
    {
        return $this->getGenreResponseModel(1, 'test-genre');
    }

    public function nullableResponseModelTypeHintAction(): ?Tests\TestApp\TestBundle\ResponseModel\Genre
    {
        return $this->getGenreResponseModel(1, 'test-genre');
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
     * @return Tests\TestApp\TestBundle\ResponseModel\Genre
     */
    public function methodWithSingleResponseModelReturnTag()
    {
        return $this->getGenreResponseModel(1, 'test-genre');
    }

    /**
     * @return Tests\TestApp\TestBundle\ResponseModel\Genre|null
     */
    public function methodWithNullableSingleResponseModelReturnTag()
    {
        return $this->getGenreResponseModel(1, 'test-genre');
    }

    /**
     * @return Tests\TestApp\TestBundle\ResponseModel\Genre[]
     */
    public function methodWithArrayOfResponseModelsReturnTag()
    {
        return [$this->getGenreResponseModel(1, 'test-genre')];
    }

    /**
     * @return Tests\TestApp\TestBundle\ResponseModel\Genre[]|null
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
     * @return Tests\TestApp\TestBundle\ResponseModel\Genre
     */
    public function detailsBySlugAction(string $slug)
    {
        return $this->getGenreResponseModel(1, $slug);
    }

    private function getGenreResponseModel(int $id, string $slug): Tests\TestApp\TestBundle\ResponseModel\Genre
    {
        $entity = new Tests\TestApp\TestBundle\Entity\Genre();
        $entity
            ->setId($id)
            ->setSlug($slug);

        return new Tests\TestApp\TestBundle\ResponseModel\Genre($entity);
    }
}
