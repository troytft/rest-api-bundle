<?php

namespace Tests\Fixture\Mapper\EnumTransformerTest;

use Tests;
use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class Model implements Mapper\ModelInterface
{
    private ?Tests\Fixture\TestApp\Enum\PolyfillStringEnum $field;

    public function getField(): ?Tests\Fixture\TestApp\Enum\PolyfillStringEnum
    {
        return $this->field;
    }

    public function setField(?Tests\Fixture\TestApp\Enum\PolyfillStringEnum $field): static
    {
        $this->field = $field;

        return $this;
    }
}
