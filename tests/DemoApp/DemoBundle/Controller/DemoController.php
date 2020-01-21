<?php

namespace Tests\DemoApp\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tests\DemoApp\DemoBundle as App;
use Tests;
use RestApiBundle\Annotation\Docs;

class DemoController extends BaseController
{
    /**
     * @Docs\Endpoint(title="Registration")
     *
     * @Route("/genre", methods="POST")
     *
     * @param App\RequestModel\ModelWithValidation $model
     *
     * @return Tests\DemoApp\DemoBundle\ResponseModel\Genre
     */
    public function registerAction()
    {
        $entity = new App\Entity\Genre();
        $entity
            ->setId(1)
            ->setSlug('test-genre');

        return new App\ResponseModel\Genre($entity);
    }
}
