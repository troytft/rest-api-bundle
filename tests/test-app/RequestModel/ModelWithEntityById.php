<?php

namespace TestApp\RequestModel;

use RestApiBundle\Annotation\Request as Mapper;
use RestApiBundle\RequestModelInterface;

class ModelWithEntityById implements RequestModelInterface
{
    /**
     * @var \TestApp\Entity\Book
     *
     * @Mapper\EntityType(class="\TestApp\Entity\Book")
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
