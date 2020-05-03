<?php

namespace Tests\TestApp\TestBundle\Controller\CommandTest\Success;

use Tests;
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
     * @return Tests\TestApp\TestBundle\ResponseModel\Genre
     */
    public function detailsBySlugAction(string $slug)
    {
        $entity = new Tests\TestApp\TestBundle\Entity\Genre();
        $entity
            ->setId(1)
            ->setSlug($slug);

        return new Tests\TestApp\TestBundle\ResponseModel\Genre($entity);
    }
}
