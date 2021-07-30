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
class EntityType implements RestApiBundle\Mapping\Mapper\TransformerAwareTypeInterface
{
    public ?bool $nullable;

    public string $class;
    public string $field;

    public function __construct($options = [], string $class = '', string $field = 'id', ?bool $nullable = null)
    {
        if (is_string($options)) {
            $this->class = $options;
            $this->field = $field;
            $this->nullable = $nullable;
        } elseif (is_array($options)) {
            $this->class = $options['value'] ?? $options['class'] ?? $class;
            $this->field = $options['field'] ?? $field;
            $this->nullable = $options['nullable'] ?? $nullable;
        } else {
            throw new \InvalidArgumentException();
        }
    }

    public function getTransformerClass(): string
    {
        return RestApiBundle\Services\Mapper\Transformer\EntityTransformer::class;
    }

    public function getTransformerOptions(): array
    {
        return [
            RestApiBundle\Services\Mapper\Transformer\EntityTransformer::CLASS_OPTION => $this->class,
            RestApiBundle\Services\Mapper\Transformer\EntityTransformer::FIELD_OPTION => $this->field,
        ];
    }

    public function getIsNullable(): ?bool
    {
        return $this->nullable;
    }
}
