<?php

namespace RestApiBundle\Mapping\Mapper;

trait NullableTrait
{
    public bool $nullable = false;

    public function getIsNullable(): bool
    {
        return $this->nullable;
    }
}
