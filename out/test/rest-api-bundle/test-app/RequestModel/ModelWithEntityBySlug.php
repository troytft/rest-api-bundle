<?php

namespace TestApp\RequestModel;

use RestApiBundle\Mapping\Mapper as Mapper;

class ModelWithEntityBySlug implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    /**
     * @var \TestApp\Entity\Book
     *
     * @Mapper\EntityType(class="\TestApp\Entity\Book", field="slug")
     */
    private $book;

    public function getBook(): \TestApp\Entity\Book
    {
        return $this->book;
    }

    public function setBook(\TestApp\Entity\Book $book)
    {
        $this->book = $book;

        return $this;
    }
}
