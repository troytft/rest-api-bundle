<?php

namespace RestApiBundle\Model\Mapper\Types;

use RestApiBundle;

class ModelType extends RestApiBundle\Model\Mapper\Types\BaseNullableType implements RestApiBundle\Model\Mapper\Types\TypeInterface
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
