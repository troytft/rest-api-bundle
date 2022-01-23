<?php

namespace Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\ResponseModel;

use Tests;
use RestApiBundle;

class Author implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    public function getId(): int
    {
        return 0;
    }

    public function getName(): string
    {
        return '';
    }

    public function getSurname(): string
    {
        return '';
    }

    public function getBirthday(): ?\DateTime
    {
        return null;
    }

    /**
     * @return Tests\Fixture\OpenApi\GenerateDocumentationCommandTest\TestSuccess\ResponseModel\Genre[]
     */
    public function getGenres(): array
    {
        return [];
    }
}
