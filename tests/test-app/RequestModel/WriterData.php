<?php

namespace TestApp\RequestModel;

use TestApp;
use RestApiBundle\Annotation\Request as Mapper;
use RestApiBundle\RequestModelInterface;
use Symfony\Component\Validator\Constraints as Assert;

class WriterData implements RequestModelInterface
{
    /**
     * @var string
     *
     * @Mapper\StringType()
     * @Assert\Length(min=1, max=255, allowEmptyString=false)
     */
    private $name;

    /**
     * @var string
     *
     * @Mapper\StringType()
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
     * @var TestApp\Entity\Genre[]
     *
     * @Mapper\ArrayOfEntitiesType(class="TestApp\Entity\Genre")
     */
    private $genres;

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

    public function getBirthday(): ?\DateTime
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTime $birthday)
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
