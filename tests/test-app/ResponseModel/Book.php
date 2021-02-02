<?php

namespace TestApp\ResponseModel;

use TestApp;
use RestApiBundle;

class Book implements RestApiBundle\ResponseModelInterface
{
    public function getId(): int
    {
        return 0;
    }

    public function getAuthor(): TestApp\ResponseModel\Author
    {
        return new TestApp\ResponseModel\Author();
    }

    public function getGenre(): ?TestApp\ResponseModel\Genre
    {
        return null;
    }
}
