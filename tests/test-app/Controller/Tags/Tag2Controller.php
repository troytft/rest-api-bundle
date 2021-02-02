<?php

namespace TestApp\Controller\Tags;

use TestApp;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Annotation\Docs;

/**
 * @Route("/tag2")
 */
class Tag2Controller
{
    /**
     * @Docs\Endpoint(title="Genre response model details", tags={"tag2"})
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
