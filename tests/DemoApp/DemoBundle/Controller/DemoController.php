<?php

namespace Tests\DemoApp\DemoBundle\Controller;

use Tests;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Annotation\Docs;

class DemoController extends BaseController
{
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
