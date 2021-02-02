<?php

namespace TestApp\ResponseModel;

use TestApp;
use RestApiBundle;

class Author implements RestApiBundle\ResponseModelInterface
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
     * @return TestApp\ResponseModel\Genre[]
     */
    public function getGenres(): array
    {
        return [];
    }
}
