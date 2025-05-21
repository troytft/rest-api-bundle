<?php

namespace Tests\Fixture\ResponseModel\PropertyAndMethodSameNameTest;

use RestApiBundle;
use Tests;

class Inner implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    public function __construct(private Tests\Fixture\TestApp\Entity\Book $book)
    {
    }

    public function getId(): int
    {
        return $this->book->getId();
    }
}
