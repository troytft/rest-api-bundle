<?php

namespace TestApp\RequestModel;

use RestApiBundle\Annotation\Request as Mapper;
use RestApiBundle\RequestModelInterface;

class ModelWithEntityBySlug implements RequestModelInterface
{
    /**
     * @var \TestApp\Entity\Genre
     *
     * @Mapper\EntityType(class="\TestApp\Entity\Genre", field="slug")
     */
    private $genre;

    public function getGenre(): \TestApp\Entity\Genre
    {
        return $this->genre;
    }

    public function setGenre(\TestApp\Entity\Genre $genre)
    {
        $this->genre = $genre;

        return $this;
    }
}
