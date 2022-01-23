<?php

namespace Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\RequestModel;

use Tests;
use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class BookList implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    public ?int $offset;
    public ?int $limit;

    /**
     * @var Tests\Fixture\TestApp\Enum\BookStatus[]|null
     */
    public ?array $statuses;

    /** @Mapper\Expose */
    public ?Tests\Fixture\TestApp\Entity\Author $author;
}
