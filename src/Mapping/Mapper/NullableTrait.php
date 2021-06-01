<?php

namespace RestApiBundle\Mapping\Mapper;

trait NullableTrait
{
    public ?bool $nullable = null;

    public function getNullable(): ?bool
    {
        return $this->nullable;
    }
}
