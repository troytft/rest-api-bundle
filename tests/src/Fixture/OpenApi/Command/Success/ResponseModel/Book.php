<?php

namespace Tests\Fixture\OpenApi\Command\Success\ResponseModel;

use Tests;
use RestApiBundle;

class Book implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    public function __construct(private Tests\Fixture\Common\Entity\Book $data)
    {
    }

    public function getId(): int
    {
        return $this->data->getId();
    }

    public function getTitle(): string
    {
        return $this->data->getTitle();
    }

    public function getAuthor(): Tests\Fixture\OpenApi\Command\Success\ResponseModel\Author
    {
        return new Tests\Fixture\OpenApi\Command\Success\ResponseModel\Author();
    }

    public function getGenre(): ?Tests\Fixture\OpenApi\Command\Success\ResponseModel\Genre
    {
        return null;
    }

    public function getStatus(): Tests\Fixture\Common\Enum\BookStatus
    {
        return Tests\Fixture\Common\Enum\BookStatus::from($this->data->getStatus());
    }

    public function getReleaseDate(): RestApiBundle\Mapping\ResponseModel\SerializableDate
    {
        return RestApiBundle\Mapping\ResponseModel\SerializableDate::from(new \DateTime('2012-03-17'));
    }
}
