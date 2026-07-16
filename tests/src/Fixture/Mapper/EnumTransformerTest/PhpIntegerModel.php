<?php

namespace Tests\Fixture\Mapper\EnumTransformerTest;

use RestApiBundle\Mapping\Mapper;
use Tests;

#[Mapper\ExposeAll]
class PhpIntegerModel implements Mapper\ModelInterface
{
    private ?Tests\Fixture\TestApp\Enum\NamespaceExample\PhpInteger $field;

    public function getField(): ?Tests\Fixture\TestApp\Enum\NamespaceExample\PhpInteger
    {
        return $this->field;
    }

    public function setField(?Tests\Fixture\TestApp\Enum\NamespaceExample\PhpInteger $field): static
    {
        $this->field = $field;

        return $this;
    }
}
