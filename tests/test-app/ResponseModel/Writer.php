<?php

namespace TestApp\ResponseModel;

use TestApp;
use RestApiBundle;

use function array_map;

class Writer implements RestApiBundle\ResponseModelInterface
{
    /**
     * @var \TestApp\Entity\Writer
     */
    private $writer;

    public function __construct(\TestApp\Entity\Writer $writer)
    {
        $this->writer = $writer;
    }

    public function getId(): int
    {
        return $this->writer->getId();
    }

    public function getName(): string
    {
        return $this->writer->getName();
    }

    public function getSurname(): string
    {
        return $this->writer->getSurname();
    }

    public function getBirthday(): ?\DateTime
    {
        return $this->writer->getBirthday();
    }

    /**
     * @return TestApp\ResponseModel\Genre[]
     */
    public function getGenres(): array
    {
        return array_map(function ($genre) {
            return new TestApp\ResponseModel\Genre($genre);
        }, $this->writer->getGenres());
    }
}
