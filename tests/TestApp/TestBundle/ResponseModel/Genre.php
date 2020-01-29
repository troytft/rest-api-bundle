<?php

namespace Tests\TestApp\TestBundle\ResponseModel;

use RestApiBundle;

class Genre implements RestApiBundle\ResponseModelInterface
{
    /**
     * @var \Tests\TestApp\TestBundle\Entity\Genre
     */
    private $genre;

    public function __construct(\Tests\TestApp\TestBundle\Entity\Genre $genre)
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
