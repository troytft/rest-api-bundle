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
     * @Route("/genres/by-slug/{slug}", methods="GET", requirements={"slug": "[\w-]+"})
     *
     * @return TestApp\ResponseModel\Genre
     */
    public function detailsBySlugAction(string $slug)
    {
        $entity = new TestApp\Entity\Genre();
        $entity
            ->setId(1)
            ->setSlug($slug);

        return new TestApp\ResponseModel\Genre($entity);
    }
}
