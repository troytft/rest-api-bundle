<?php

namespace Tests\Fixture\Mapper\AutoConvertToEntitiesCollection;

use RestApiBundle\Mapping\Mapper as Mapper;

class Model implements Mapper\ModelInterface
{
    /**
     * @var string[]|null
     *
     * @Mapper\ArrayType(type=@Mapper\EntityType(class="Tests\Fixture\Common\Entity\Book"))
     */
    private ?array $books = null;

    public function getBooks(): ?array
    {
        return $this->books;
    }

    public function setBooks(?array $books)
    {
        $this->books = $books;

        return $this;
    }
}
