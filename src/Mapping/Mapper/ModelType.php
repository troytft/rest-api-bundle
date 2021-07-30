<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;
use function is_array;
use function is_string;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ModelType implements RestApiBundle\Mapping\Mapper\TypeInterface
{
    public ?bool $nullable;

    public string $class;

    public function __construct(array $options = [], string $class = '', ?bool $nullable = null)
    {
        $this->class = $options['class'] ?? $class;
        $this->nullable = $options['nullable'] ?? $nullable;

        if (is_string($options)) {
            $this->class = $options;
            $this->nullable = $nullable;
        } elseif (is_array($options)) {
            $this->class = $options['value'] ?? $options['class'] ?? $class;
            $this->nullable = $options['nullable'] ?? $nullable;
        } else {
            throw new \InvalidArgumentException();
        }
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
