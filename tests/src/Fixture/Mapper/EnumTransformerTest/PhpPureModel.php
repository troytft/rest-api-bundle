<?php

namespace Tests\Fixture\Mapper\EnumTransformerTest;

use RestApiBundle\Mapping\Mapper;
use Tests;

#[Mapper\ExposeAll]
class PhpPureModel implements Mapper\ModelInterface
{
    private ?Tests\Fixture\TestApp\Enum\NamespaceExample\PhpPure $field;

    public function getField(): ?Tests\Fixture\TestApp\Enum\NamespaceExample\PhpPure
    {
        return $this->field;
    }

    public function setField(?Tests\Fixture\TestApp\Enum\NamespaceExample\PhpPure $field): static
    {
        $this->field = $field;

        return $this;
    }
}
