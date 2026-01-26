<?php

namespace Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\RequestModel;

use RestApiBundle\Mapping\Mapper;
use Symfony\Component\Validator\Constraints as Assert;
use Tests;

#[Mapper\ExposeAll]
class BookList implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    public ?int $offset;

    public ?int $limit;

    public ?Tests\Fixture\TestApp\Entity\Author $author;

    public ?Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString $polyfillStringEnum;

    public ?Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString $phpStringEnum;

    #[Assert\Choice(choices: [
        Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString::PUBLISHED,
        Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString::CREATED,
    ])]
    public ?Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString $phpStringEnumLimited;
}
