<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class EntityType implements RestApiBundle\Mapping\Mapper\TransformerAwareTypeInterface
{
    public bool $nullable = false;

    public string $class;
    public string $field;

    public function __construct(array $options = [], string $class = '', string $field = 'id', bool $nullable = false)
    {
        $this->class = $options['class'] ?? $class;
        $this->field = $options['field'] ?? $field;
        $this->nullable = $options['nullable'] ?? $nullable;
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

    public function getIsNullable(): bool
    {
        return $this->nullable;
    }
}
