<?php

namespace Tests\Fixture\Mapper\EnumTransformerTest;

use Tests;
use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class Model implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    private ?Tests\Fixture\Common\Enum\BookStatus $value;

    public function getValue(): ?Tests\Fixture\Common\Enum\BookStatus
    {
        return $this->value;
    }

    public function setValue(?Tests\Fixture\Common\Enum\BookStatus $value): static
    {
        $this->value = $value;

        return $this;
    }
}
