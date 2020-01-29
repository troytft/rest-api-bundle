<?php

namespace Tests\TestApp\TestBundle\Controller\Tags;

use Tests;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Annotation\Docs;

/**
 * @Route("/tag2")
 */
class Tag2Controller extends BaseController
{
    /**
     * @Docs\Endpoint(title="Genre response model details", tags={"tag2"})
     *
     * @Route(methods="GET")
     */
    public function getGenreAction(): Tests\TestApp\TestBundle\ResponseModel\Genre
    {
        return $this->getGenreResponseModel(1, 'test-genre');
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
