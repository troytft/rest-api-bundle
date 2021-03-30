<?php

namespace TestApp\RequestModel;

use TestApp;
use RestApiBundle\Annotation\Request as Mapper;
use RestApiBundle\Mapping\RequestModel\RequestModelInterface;

class ModelWithArrayOfEntities implements RequestModelInterface
{
    /**
     * @var \TestApp\Entity\Book[]
     *
     * @Mapper\ArrayOfEntitiesType(class="TestApp\Entity\Book")
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
