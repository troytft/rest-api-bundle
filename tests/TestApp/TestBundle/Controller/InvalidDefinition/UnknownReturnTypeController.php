<?php

namespace Tests\TestApp\TestBundle\Controller\InvalidDefinition;

use Tests;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Annotation\Docs;

/**
 * @Route("/invalid-definition")
 */
class UnknownReturnTypeController extends BaseController
{
    /**
     * @Docs\Endpoint(title="Genre response model details", tags={"tag1"})
     *
     * @Route(methods="GET")
     */
    public function getGenreAction()
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
