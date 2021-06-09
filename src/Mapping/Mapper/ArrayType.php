<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

/**
 * @Annotation
 */
class ArrayType implements RestApiBundle\Mapping\Mapper\TypeInterface
{
    public bool $nullable = false;

    /**
     * Type hint forced to object, cause annotation reader doesn't support interfaces
     *
     * @var object
     */
    public $type;

    public function getValuesType(): RestApiBundle\Mapping\Mapper\TypeInterface
    {
        return $this->type;
    }

    public function getIsNullable(): bool
    {
        return $this->nullable;
    }
}
