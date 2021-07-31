<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

abstract class BaseNullableType implements RestApiBundle\Mapping\Mapper\NullableAwareTypeInterface
{
    public function __construct(private ?bool $nullable = null)
    {
    }

    public function getIsNullable(): ?bool
    {
        return $this->nullable;
    }

    public function setIsNullable(?bool $value): static
    {
        $this->nullable = $value;

        return $this;
    }
}
