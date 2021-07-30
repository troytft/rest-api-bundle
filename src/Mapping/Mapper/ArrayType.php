<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;
use function is_array;
use function is_string;

/**
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ArrayType implements RestApiBundle\Mapping\Mapper\TypeInterface
{
    /**
     * Type hint forced to object, cause annotation reader doesn't support interfaces
     *
     * @var object
     */
    public $type;
    public ?bool $nullable = null;

    public function __construct(array $options = [], ?RestApiBundle\Mapping\Mapper\TypeInterface $type = null, ?bool $nullable = null)
    {
        if (is_string($options)) {
            $this->type = $options;
            $this->nullable = $nullable;
        } elseif (is_array($options)) {
            $this->type = $options['value'] ?? $options['type'] ?? $type;
            $this->nullable = $options['nullable'] ?? $nullable;
        } else {
            throw new \InvalidArgumentException();
        }
    }

    public function getValuesType(): RestApiBundle\Mapping\Mapper\TypeInterface
    {
        return $this->type;
    }

    public function getIsNullable(): ?bool
    {
        return $this->nullable;
    }

    public function setIsNullable(?bool $value)
    {
        $this->nullable = $value;
    }
}
