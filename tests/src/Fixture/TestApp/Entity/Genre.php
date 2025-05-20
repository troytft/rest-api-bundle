<?php

declare(strict_types=1);

namespace Tests\Fixture\TestApp\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 *
 * @ORM\Entity()
 */
class Genre
{
    /**
     * @var int
     *
     * @ORM\Id()
     *
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    public function __construct(int $id, string $title)
    {
        $this->id = $id;
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }
}
