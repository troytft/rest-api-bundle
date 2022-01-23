<?php

namespace Tests\Fixture\Mapper;

use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class Release implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    private string $country;
    private Mapper\Date $date;

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getDate(): Mapper\Date
    {
        return $this->date;
    }

    public function setDate(Mapper\Date $date): static
    {
        $this->date = $date;

        return $this;
    }
}
