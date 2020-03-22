<?php

namespace Tests\TestApp\TestBundle\Controller\PathParameters;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Routing\Annotation\Route;
use RestApiBundle\Annotation\Docs;
use Tests\TestApp\TestBundle\Entity\Genre;

class EntityPathParametersController extends BaseController
{
    /**
     * @Docs\Endpoint(title="Title", tags={"tag"})
     *
     * @Route("/{int}/{genre}/{string}/{slug}", methods="GET")
     *
     * @return null
     */
    public function testAction(int $int, Genre $genre, string $string, Genre $genre2)
    {
        return null;
    }
}
