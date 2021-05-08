<?php

namespace TestApp\RequestModel;

use RestApiBundle\Mapping\RequestModel as Mapping;

class ModelWithEntityById implements Mapping\RequestModelInterface
{
    /**
     * @var \TestApp\Entity\Book
     *
     * @Mapping\EntityType(class="\TestApp\Entity\Book")
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
