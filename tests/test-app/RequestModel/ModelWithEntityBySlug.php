<?php

namespace TestApp\RequestModel;

use TestApp;
use RestApiBundle\Mapping\Mapper;

class ModelWithEntityBySlug implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    /**
     * @var TestApp\Entity\Book
     *
     * @Mapper\EntityType(field="slug")
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
