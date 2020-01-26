<?php

namespace Tests\DemoApp\DemoBundle\Controller;

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
    public function registerAction(Tests\DemoApp\DemoBundle\RequestModel\ModelWithValidation $model): Response
    {
        if ($model->getStringField() === '') {
            throw new \InvalidArgumentException();
        }

        return new Response('ok');
    }

    public function methodWithEmptyTypeHintAction()
    {
    }

    public function notNullableResponseModelTypeHintAction(): Tests\DemoApp\DemoBundle\ResponseModel\Genre
    {
        return $this->getGenreResponseModel(1, 'test-genre');
    }

    public function nullableResponseModelTypeHintAction(): ?Tests\DemoApp\DemoBundle\ResponseModel\Genre
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
     * @return Tests\DemoApp\DemoBundle\ResponseModel\Genre
     */
    public function methodWithSingleResponseModelReturnTag()
    {
        return $this->getGenreResponseModel(1, 'test-genre');
    }

    /**
     * @return Tests\DemoApp\DemoBundle\ResponseModel\Genre|null
     */
    public function methodWithNullableSingleResponseModelReturnTag()
    {
        return $this->getGenreResponseModel(1, 'test-genre');
    }

    /**
     * @return Tests\DemoApp\DemoBundle\ResponseModel\Genre[]
     */
    public function methodWithArrayOfResponseModelsReturnTag()
    {
        return [$this->getGenreResponseModel(1, 'test-genre')];
    }

    /**
     * @return Tests\DemoApp\DemoBundle\ResponseModel\Genre[]|null
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
     * @return Tests\DemoApp\DemoBundle\ResponseModel\Genre
     */
    public function detailsBySlugAction(string $slug)
    {
        return $this->getGenreResponseModel(1, $slug);
    }

    private function getGenreResponseModel(int $id, string $slug): Tests\DemoApp\DemoBundle\ResponseModel\Genre
    {
        $entity = new Tests\DemoApp\DemoBundle\Entity\Genre();
        $entity
            ->setId($id)
            ->setSlug($slug);

        return new Tests\DemoApp\DemoBundle\ResponseModel\Genre($entity);
    }
}
