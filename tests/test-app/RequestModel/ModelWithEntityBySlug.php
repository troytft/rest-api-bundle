<?php

namespace TestApp\RequestModel;

use RestApiBundle\Annotation\Request as Mapper;
use RestApiBundle\RequestModelInterface;

class ModelWithEntityBySlug implements RequestModelInterface
{
    /**
     * @var \TestApp\Entity\Book
     *
     * @Mapper\EntityType(class="\TestApp\Entity\Book", field="slug")
     */
    private $genre;

    public function getGenre(): \TestApp\Entity\Book
    {
        return $this->genre;
    }

    public function setGenre(\TestApp\Entity\Book $genre)
    {
        $this->genre = $genre;

        return $this;
    }
}
