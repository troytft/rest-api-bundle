<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

class ModelType extends RestApiBundle\Mapping\Mapper\BaseNullableType implements RestApiBundle\Mapping\Mapper\TypeInterface
{
    public function __construct(
        private string $class = '',
        ?bool $nullable = null
    ) {
        parent::__construct(nullable: $nullable);
    }

    public function getClass(): string
    {
        return $this->class;
    }
}
