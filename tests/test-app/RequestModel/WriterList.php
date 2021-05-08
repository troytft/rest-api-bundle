<?php

namespace TestApp\RequestModel;

use TestApp;
use RestApiBundle\Mapping\RequestModel;
use Symfony\Component\Validator\Constraints as Assert;

class WriterList implements RequestModel\RequestModelInterface
{
    /**
     * @var int
     *
     * @RequestModel\IntegerType()
     */
    private $offset;

    /**
     * @var int
     *
     * @RequestModel\IntegerType()
     */
    private $limit;

    /**
     * @var string|null
     *
     * @RequestModel\StringType(nullable=true)
     * @Assert\Length(min=1, max=255, allowEmptyString=false)
     */
    private $name;

    /**
     * @var string|null
     *
     * @RequestModel\StringType(nullable=true)
     * @Assert\Length(min=1, max=255, allowEmptyString=false)
     */
    private $surname;

    /**
     * @var \DateTime|null
     *
     * @RequestModel\DateType(nullable=true)
     */
    private $birthday;

    /**
     * @var TestApp\Entity\Book[]|null
     *
     * @RequestModel\ArrayOfEntitiesType(class="TestApp\Entity\Book", nullable=true)
     */
    private $genres;

    /**
     * @var TestApp\Entity\Book|null
     *
     * @RequestModel\EntityType(class="TestApp\Entity\Book", nullable=true)
     */
    private $writer;

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $offset)
    {
        $this->offset = $offset;

        return $this;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname)
    {
        $this->surname = $surname;

        return $this;
    }

    public function getBirthday(): ?\DateTime
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTime $birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getGenres(): ?array
    {
        return $this->genres;
    }

    public function setGenres(?array $genres)
    {
        $this->genres = $genres;

        return $this;
    }
}
