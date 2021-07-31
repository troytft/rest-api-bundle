<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

class ArrayType extends BaseNullableType
{
    public function __construct(
        private RestApiBundle\Mapping\Mapper\TypeInterface $valuesType,
        ?bool $nullable = null
    ) {
        parent::__construct(nullable: $nullable);
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
}
