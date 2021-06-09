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
    use NullableTrait;

    public string $class;

    public function __construct(array $options = [], string $class = '', bool $nullable = false)
    {
        $this->class = $options['class'] ?? $class;
        $this->nullable = $options['nullable'] ?? $nullable;
    }
}
