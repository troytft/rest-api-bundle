<?php

namespace Tests\Fixture\Mapper\EnumTransformerTest;

use RestApiBundle\Mapping\Mapper;
use Tests;

#[Mapper\ExposeAll]
class PhpStringModel implements Mapper\ModelInterface
{
    private ?Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString $field;

    public function getField(): ?Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString
    {
        return $this->field;
    }

    public function setField(?Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString $field): static
    {
        $this->field = $field;

        return $this;
    }
}
