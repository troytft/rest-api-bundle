<?php

namespace Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\RequestModel;

use Tests;
use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class BookList implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    public ?int $offset;

    public ?int $limit;

    public ?Tests\Fixture\TestApp\Entity\Author $author;

    public ?Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\RequestModel\Coordinates $coordinates;
}
