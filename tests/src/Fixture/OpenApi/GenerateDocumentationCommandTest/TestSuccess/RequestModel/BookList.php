<?php

namespace Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\RequestModel;

use Symfony\Component\Validator\Constraints as Assert;
use Tests;
use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class BookList implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    public ?int $offset;

    public ?int $limit;

    public ?Tests\Fixture\TestApp\Entity\Author $author;

    public ?Tests\Fixture\TestApp\Enum\PolyfillStringEnum $polyfillStringEnum;

    public ?Tests\Fixture\TestApp\Enum\PhpStringEnum $phpStringEnum;

    #[Assert\Choice(choices: [
        Tests\Fixture\TestApp\Enum\PhpStringEnum::PUBLISHED,
        Tests\Fixture\TestApp\Enum\PhpStringEnum::CREATED,
    ])]
    public ?Tests\Fixture\TestApp\Enum\PhpStringEnum $phpStringEnumLimited;
}
