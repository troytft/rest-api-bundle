<?php

namespace RestApiBundle\Mapping\RequestModel;

trait NullableTrait
{
    public ?bool $nullable = null;

    public function getNullable(): ?bool
    {
        return $this->nullable;
    }
}
