<?php

namespace TestApp\Controller\CommandTest\Success;

use TestApp;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Annotation\Docs;

class DefaultController
{
    /**
     * @Docs\Endpoint(title="Set by author and genre", tags={"demo"})
     *
     * @Route("/{author}/{slug}/genres", methods="GET", requirements={"slug": "[\w-]+"})
     */
    public function byAuthorAndGenreAction(TestApp\Entity\Genre $genre, TestApp\Entity\Author $author, TestApp\RequestModel\UpdateGenres $requestModel): void
    {
    }

    /**
     * @Docs\Endpoint(title="Get by author and genre", tags={"demo"})
     *
     * @Route("/{author}/{slug}/genres", methods="PUT", requirements={"slug": "[\w-]+"})
     *
     * @return TestApp\ResponseModel\Genre[]
     */
    public function getByAuthorAndGenreAction(TestApp\Entity\Genre $genre, TestApp\Entity\Author $author)
    {
        return [];
    }
}
