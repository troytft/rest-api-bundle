<?php

namespace Tests\Fixture\Mapper;

use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class Release implements Mapper\ModelInterface
{
    private ?string $country;
    private ?Mapper\DateInterface $date;

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getDate(): ?Mapper\DateInterface
    {
        return $this->date;
    }

    public function setDate(?Mapper\DateInterface $date): static
    {
        $this->date = $date;

        return $this;
    }
}
