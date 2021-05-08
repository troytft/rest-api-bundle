<?php

namespace TestApp\RequestModel;

use TestApp;
use RestApiBundle\Mapping\RequestModel as Mapping;
use Symfony\Component\Validator\Constraints as Assert;

class WriterData implements Mapping\RequestModelInterface
{
    /**
     * @var string
     *
     * @Mapping\StringType()
     * @Assert\Length(min=1, max=255, allowEmptyString=false)
     */
    private $name;

    /**
     * @var string
     *
     * @Mapping\StringType()
     * @Assert\Length(min=1, max=255, allowEmptyString=false)
     */
    private $surname;

    /**
     * @var \DateTime|null
     *
     * @Mapping\DateType(nullable=true)
     */
    private $birthday;

    /**
     * @var TestApp\Entity\Book[]
     *
     * @Mapping\ArrayOfEntitiesType(class="TestApp\Entity\Book")
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
