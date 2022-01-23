<?php

namespace Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\ResponseModel;

use Tests;
use RestApiBundle;

class Book implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    public function __construct(private Tests\Fixture\TestApp\Entity\Book $data)
    {
    }

    public function getId(): int
    {
        return $this->data->getId();
    }

    public function getCreatedAt(): \DateTime
    {
        return new \DateTime();
    }

    public function getTitle(): string
    {
        return $this->data->getTitle();
    }

    public function getAuthor(): Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\ResponseModel\Author
    {
        return new Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\ResponseModel\Author();
    }

    public function getGenre(): ?Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\ResponseModel\Genre
    {
        return null;
    }

    public function getStatus(): Tests\Fixture\TestApp\Enum\BookStatus
    {
        return Tests\Fixture\TestApp\Enum\BookStatus::from($this->data->getStatus());
    }

    public function getReleaseDate(): RestApiBundle\Mapping\ResponseModel\Date
    {
        return RestApiBundle\Mapping\ResponseModel\Date::from(new \DateTime('2012-03-17'));
    }
}