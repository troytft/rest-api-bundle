<?php

declare(strict_types=1);

namespace Tests\Fixture\TestApp\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table
 *
 * @ORM\Entity(repositoryClass="Tests\Fixture\TestApp\Repository\AuthorRepository")
 */
class Author
{
    /**
     * @ORM\Id
     *
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @ORM\Column(name="id", type="integer")
     */
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
