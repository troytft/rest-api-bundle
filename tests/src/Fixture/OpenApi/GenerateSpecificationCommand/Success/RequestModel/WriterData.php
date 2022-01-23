<?php

namespace Tests\Fixture\OpenApi\GenerateSpecificationCommand\Success\RequestModel;

use Tests;
use RestApiBundle\Mapping\Mapper;
use Symfony\Component\Validator\Constraints as Assert;

#[Mapper\ExposeAll]
class WriterData implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    private string $name;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    private string $surname;
    
    private ?Mapper\Date $birthday;

    /**
     * @var Tests\Fixture\TestApp\Entity\Genre[]
     */
    private array $genres;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function setSurname(string $surname)
    {
        $this->surname = $surname;

        return $this;
    }

    public function getBirthday(): ?Mapper\Date
    {
        return $this->birthday;
    }

    public function setBirthday(?Mapper\Date $birthday): static
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getGenres(): array
    {
        return $this->genres;
    }

    public function setGenres(array $genres)
    {
        $this->genres = $genres;

        return $this;
    }
}
