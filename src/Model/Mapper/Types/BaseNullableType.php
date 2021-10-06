<?php

namespace RestApiBundle\Model\Mapper\Types;

use RestApiBundle;

abstract class BaseNullableType implements RestApiBundle\Model\Mapper\Types\NullableAwareTypeInterface
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