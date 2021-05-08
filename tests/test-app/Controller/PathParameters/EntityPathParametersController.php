<?php

namespace TestApp\Controller\PathParameters;

use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Mapping\OpenApi;
use TestApp\Entity\Book;

class EntityPathParametersController
{
    /**
     * @OpenApi\Endpoint(title="Title", tags={"tag"})
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
