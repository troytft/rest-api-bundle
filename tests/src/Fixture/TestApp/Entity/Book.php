<?php

namespace Tests\Fixture\TestApp\Entity;

use Doctrine\ORM\Mapping as ORM;
use Tests\Fixture\TestApp\Enum\PolyfillStringEnum;

#[ORM\Entity]
#[ORM\Table(name: 'books')]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id', type: 'integer')]
    private int $id;

    #[ORM\Column]
    private string $slug;

    #[ORM\Column]
    private string $title;

    private string $status = PolyfillStringEnum::PUBLISHED;

    public function __construct(int $id, string $slug, string $title)
    {
        $this->id = $id;
        $this->slug = $slug;
        $this->title = $title;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
