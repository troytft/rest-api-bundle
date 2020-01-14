<?php

namespace Tests\DemoApp\DemoBundle\ResponseModel;

use RestApiBundle;

class Genre implements RestApiBundle\ResponseModelInterface
{
    /**
     * @var \Tests\DemoApp\DemoBundle\Entity\Genre
     */
    private $genre;

    public function __construct(\Tests\DemoApp\DemoBundle\Entity\Genre $genre)
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
