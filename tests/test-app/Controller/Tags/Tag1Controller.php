<?php

namespace TestApp\Controller\Tags;

use TestApp;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Annotation\Docs;

/**
 * @Route("/tag1")
 */
class Tag1Controller
{
    /**
     * @Docs\Endpoint(title="Book response model details", tags={"tag1"})
     *
     * @Route(methods="GET")
     */
    public function getGenreAction(): TestApp\ResponseModel\Genre
    {
        return $this->getGenreResponseModel(1, 'test-genre');
    }

    private function getGenreResponseModel(int $id, string $slug): TestApp\ResponseModel\Genre
    {
        $entity = new TestApp\Entity\Book();
        $entity
            ->setId($id)
            ->setSlug($slug);

        return new TestApp\ResponseModel\Genre($entity);
    }
}
