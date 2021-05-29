<?php

namespace RestApiBundle\Mapping\RequestModel;

trait NullableTrait
{
    public ?bool $nullable;

    public function getNullable(): ?bool
    {
        return $this->nullable;
    }
}
