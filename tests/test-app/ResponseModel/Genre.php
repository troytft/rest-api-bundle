<?php

namespace TestApp\ResponseModel;

use RestApiBundle;
use TestApp;

class Genre implements RestApiBundle\ResponseModelInterface
{
    /**
     * @var \TestApp\Entity\Book
     */
    private $genre;

    public function __construct(\TestApp\Entity\Book $genre)
    {
        $this->genre = $genre;
    }

    public function getId(): int
    {
        return $this->genre->getId();
    }

    public function getSlug(): string
    {
        return $this->genre->getSlug();
    }
}
