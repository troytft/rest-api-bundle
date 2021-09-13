<?php

namespace RestApiBundle\Model\Mapper\Types;

use RestApiBundle;

class ArrayType extends BaseNullableType
{
    public function __construct(
        private RestApiBundle\Model\Mapper\Types\TypeInterface $valuesType,
        ?bool $nullable = null
    ) {
        parent::__construct(nullable: $nullable);
    }

    public function getValuesType(): RestApiBundle\Model\Mapper\Types\TypeInterface
    {
        return $this->valuesType;
    }

    public function setValuesType(RestApiBundle\Model\Mapper\Types\TypeInterface $valuesType): static
    {
        $this->valuesType = $valuesType;

        return $this;
    }
}
