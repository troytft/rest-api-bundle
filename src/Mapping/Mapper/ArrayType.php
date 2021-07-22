<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

/**
 * @Annotation
 */
class ArrayType implements RestApiBundle\Mapping\Mapper\TypeInterface
{
    /**
     * Type hint forced to object, cause annotation reader doesn't support interfaces
     *
     * @var object
     */
    public $type;
    public bool $nullable = false;

    public function __construct(array $options = [])
    {
        if (isset($options['value'])) {
            $this->type = $options['value'];
        } elseif (isset($options['type'])) {
            $this->type = $options['type'];
        } else {
            throw new \LogicException();
        }
    }

    public function getValuesType(): RestApiBundle\Mapping\Mapper\TypeInterface
    {
        return $this->type;
    }

    public function getIsNullable(): bool
    {
        return $this->nullable;
    }
}
