<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

class ArrayType implements RestApiBundle\Mapping\Mapper\TypeInterface
{
    public function __construct(
        private RestApiBundle\Mapping\Mapper\TypeInterface $valuesType,
        private ?bool $nullable = null
    ) {
    }

    public function getValuesType(): RestApiBundle\Mapping\Mapper\TypeInterface
    {
        return $this->valuesType;
    }

    public function setValuesType(RestApiBundle\Mapping\Mapper\TypeInterface $valuesType): static
    {
        $this->valuesType = $valuesType;

        return $this;
    }

    public function getIsNullable(): ?bool
    {
        return $this->nullable;
    }

    public function setIsNullable(?bool $value): static
    {
        $this->nullable = $value;
    }
}
