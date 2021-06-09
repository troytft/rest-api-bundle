<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class ModelType implements RestApiBundle\Mapping\Mapper\TypeInterface
{
    public bool $nullable = false;

    public string $class;

    public function __construct(array $options = [], string $class = '', bool $nullable = false)
    {
        $this->class = $options['class'] ?? $class;
        $this->nullable = $options['nullable'] ?? $nullable;
    }

    public function getIsNullable(): bool
    {
        return $this->nullable;
    }
}
