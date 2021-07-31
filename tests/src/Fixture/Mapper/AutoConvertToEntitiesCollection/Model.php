<?php

namespace Tests\Fixture\Mapper\AutoConvertToEntitiesCollection;

use TestApp;
use RestApiBundle\Mapping\Mapper as Mapper;

class Model implements Mapper\ModelInterface
{
    /**
     * @var TestApp\Entity\Book[]|null
     *
     * @Mapper\AutoType
     */
    private ?array $books = null;

    /**
     * @return TestApp\Entity\Book[]|null
     */
    public function getBooks(): ?array
    {
        return $this->books;
    }

    /**
     * @param TestApp\Entity\Book|null $books
     */
    public function setBooks(?array $books): static
    {
        $this->books = $books;

        return $this;
    }
}
