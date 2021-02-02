<?php

namespace TestApp\Controller\Tags;

use TestApp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Annotation\Docs;

/**
 * @Route("/tag1")
 */
class Tag1Controller extends AbstractController
{
    /**
     * @Docs\Endpoint(title="Genre response model details", tags={"tag1"})
     *
     * @Route(methods="GET")
     */
    public function getGenreAction(): TestApp\ResponseModel\Genre
    {
        return $this->getGenreResponseModel(1, 'test-genre');
    }

    private function getGenreResponseModel(int $id, string $slug): TestApp\ResponseModel\Genre
    {
        $entity = new TestApp\Entity\Genre();
        $entity
            ->setId($id)
            ->setSlug($slug);

        return new TestApp\ResponseModel\Genre($entity);
    }
}
