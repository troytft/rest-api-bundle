<?php

namespace TestApp\RequestModel\DoctrineEntityTransformerMultipleTest;

use TestApp;
use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class Model implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    /**
     * @var TestApp\Entity\Book[]
     */
    private array $books;

    /**
     * @return TestApp\Entity\Book[]
     */
    public function getBooks(): array
    {
        return $this->books;
    }

    public function setBooks(array $books)
    {
        $this->books = $books;

        return $this;
    }
}
