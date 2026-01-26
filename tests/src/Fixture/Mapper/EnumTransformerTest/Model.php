<?php

namespace Tests\Fixture\Mapper\EnumTransformerTest;

use RestApiBundle\Mapping\Mapper;
use Tests;

#[Mapper\ExposeAll]
class Model implements Mapper\ModelInterface
{
    private ?Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString $field;

    public function getField(): ?Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString
    {
        return $this->field;
    }

    public function setField(?Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString $field): static
    {
        $this->field = $field;

        return $this;
    }
}
