<?php

namespace TestApp\ResponseModel;

use TestApp;
use RestApiBundle;

class Book implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    /**
     * @var TestApp\Entity\Book
     */
    private $book;

    public function __construct(TestApp\Entity\Book $book)
    {
        $this->book = $book;
    }

    public function getId(): int
    {
        return $this->book->getId();
    }

    public function getTitle(): string
    {
        return $this->book->getTitle();
    }

    public function getAuthor(): TestApp\ResponseModel\Author
    {
        return new TestApp\ResponseModel\Author();
    }

    public function getGenre(): ?TestApp\ResponseModel\Genre
    {
        return null;
    }

    public function getStatus(): TestApp\Enum\BookStatus
    {
        return TestApp\Enum\BookStatus::from($this->book->getStatus());
    }

    public function getReleaseDate(): RestApiBundle\Mapping\ResponseModel\SerializableDate
    {
        return RestApiBundle\Mapping\ResponseModel\SerializableDate::from(new \DateTime('2012-03-17'));
    }
}
