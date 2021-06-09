<?php

namespace TestApp\RequestModel;

use TestApp;
use RestApiBundle\Mapping\Mapper as Mapper;

class ModelWithArrayOfEntities implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    /**
     * @var \TestApp\Entity\Book[]
     *
     * @Mapper\ArrayType(type=@Mapper\EntityType(class="TestApp\Entity\Book"))
     */
    private $books;

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
