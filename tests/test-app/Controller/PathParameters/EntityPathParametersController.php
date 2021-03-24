<?php

namespace TestApp\Controller\PathParameters;

use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Annotation\Docs;
use TestApp\Entity\Book;

class EntityPathParametersController
{
    /**
     * @Docs\Endpoint(title="Title", tags={"tag"})
     *
     * @Route("/{int}/{genre}/{string}/{slug}", methods="GET")
     *
     * @return null
     */
    public function testAction(int $int, Book $genre, string $string, Book $genre2)
    {
        return null;
    }
}
