<?php

declare(strict_types=1);

namespace Tests\Fixture\TestApp\Entity;

use Doctrine\ORM\Mapping as ORM;
use Tests;

/**
 * @ORM\Table
 *
 * @ORM\Entity(repositoryClass="Tests\Fixture\TestApp\Repository\BookRepository")
 */
class Book
{
    /**
     * @var int
     *
     * @ORM\Id
     *
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string")
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @var string
     */
    private $status = Tests\Fixture\TestApp\Enum\PolyfillStringEnum::PUBLISHED;

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

    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug)
    {
        $this->slug = $slug;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;

        return $this;
    }
}
