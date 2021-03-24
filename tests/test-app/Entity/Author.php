<?php

namespace TestApp\Entity;

use TestApp;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="TestApp\Repository\AuthorRepository")
 */
class Author
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
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
     * @return TestApp\Entity\Book[]
     */
    public function getGenres(): array
    {
        return [];
    }
}
