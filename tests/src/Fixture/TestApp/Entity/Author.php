<?php

namespace Tests\Fixture\TestApp\Entity;

use Doctrine\ORM\Mapping as ORM;
use Tests\Fixture\TestApp\Entity\Book;

#[ORM\Entity]
#[ORM\Table(name: 'authors')]
class Author
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private int $id;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
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
     * @return Book[]
     */
    public function getGenres(): array
    {
        return [];
    }
}
