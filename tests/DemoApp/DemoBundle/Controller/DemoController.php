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
        $entity = new Tests\DemoApp\DemoBundle\Entity\Genre();
        $entity
            ->setId(1)
            ->setSlug('test-genre');

        return new Tests\DemoApp\DemoBundle\ResponseModel\Genre($entity);
    }

    public function nullableResponseModelTypeHintAction(): ?Tests\DemoApp\DemoBundle\ResponseModel\Genre
    {
        $entity = new Tests\DemoApp\DemoBundle\Entity\Genre();
        $entity
            ->setId(1)
            ->setSlug('test-genre');

        return new Tests\DemoApp\DemoBundle\ResponseModel\Genre($entity);
    }

    /**
     * @Docs\Endpoint(title="Genre response model details")
     *
     * @Route("/genre", methods="GET")
     *
     * @return Tests\DemoApp\DemoBundle\ResponseModel\Genre
     */
    public function genreAction()
    {
        $entity = new Tests\DemoApp\DemoBundle\Entity\Genre();
        $entity
            ->setId(1)
            ->setSlug('test-genre');

        return new Tests\DemoApp\DemoBundle\ResponseModel\Genre($entity);
    }
}
