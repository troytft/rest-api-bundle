<?php

namespace Tests\Fixture\Mapper\EnumTransformerTest;

use Tests;
use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class Model implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    private ?Tests\Fixture\TestApp\Enum\BookStatus $field;

    public function getField(): ?Tests\Fixture\TestApp\Enum\BookStatus
    {
        return $this->field;
    }

    public function setField(?Tests\Fixture\TestApp\Enum\BookStatus $field): static
    {
        $this->field = $field;

        return $this;
    }
}
