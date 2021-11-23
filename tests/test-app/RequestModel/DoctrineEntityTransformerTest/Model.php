<?php

namespace TestApp\RequestModel\DoctrineEntityTransformerTest;

use TestApp;
use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class Model implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    #[Mapper\FindByField('slug')]
    private ?TestApp\Entity\Book $bookBySlug;
    private ?TestApp\Entity\Book $bookById;

    public function getBookBySlug(): ?TestApp\Entity\Book
    {
        return $this->bookBySlug;
    }

    public function setBookBySlug(?TestApp\Entity\Book $bookBySlug): static
    {
        $this->bookBySlug = $bookBySlug;

        return $this;
    }

    public function getBookById(): ?TestApp\Entity\Book
    {
        return $this->bookById;
    }

    public function setBookById(?TestApp\Entity\Book $bookById): static
    {
        $this->bookById = $bookById;

        return $this;
    }
}
