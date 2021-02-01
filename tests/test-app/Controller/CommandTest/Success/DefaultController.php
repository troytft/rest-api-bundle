<?php

namespace TestApp\Controller\CommandTest\Success;

use TestApp;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Annotation\Docs;

class DefaultController extends BaseController
{
    /**
     * @Docs\Endpoint(title="Genre response model details", tags={"demo"})
     *
     * @Route("/{author}/{slug}/genres", methods="GET", requirements={"slug": "[\w-]+"})
     *
     * @return TestApp\ResponseModel\Genre[]
     */
    public function byGenreAndAuthorAction(TestApp\Entity\Genre $genre, TestApp\Entity\Author $author)
    {
        return [];
    }
}
