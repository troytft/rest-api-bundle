<?php

namespace TestApp\RequestModel;

use TestApp;
use RestApiBundle\Annotation\Request as Mapper;
use RestApiBundle\RequestModelInterface;
use Symfony\Component\Validator\Constraints as Assert;

class WriterList implements RequestModelInterface
{
    /**
     * @var int
     *
     * @Mapper\IntegerType()
     */
    private $offset;

    /**
     * @var int
     *
     * @Mapper\IntegerType()
     */
    private $limit;

    /**
     * @var string|null
     *
     * @Mapper\StringType(nullable=true)
     * @Assert\Length(min=1, max=255, allowEmptyString=false)
     */
    private $name;

    /**
     * @var string|null
     *
     * @Mapper\StringType(nullable=true)
     * @Assert\Length(min=1, max=255, allowEmptyString=false)
     */
    private $surname;

    /**
     * @var \DateTime|null
     *
     * @Mapper\DateType(nullable=true)
     */
    private $birthday;

    /**
     * @var TestApp\Entity\Genre[]|null
     *
     * @Mapper\ArrayOfEntitiesType(class="TestApp\Entity\Genre", nullable=true)
     */
    private $genres;

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
