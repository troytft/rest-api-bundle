<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class EntityType extends RestApiBundle\Mapping\Mapper\BaseNullableType implements RestApiBundle\Mapping\Mapper\TransformerAwareTypeInterface
{
    public function __construct(
        array $options = [],
        private string $class = '',
        public string $field = 'id',
        ?bool $nullable = null
    ) {
        $this->field = $options['field'] ?? $field;

        parent::__construct(nullable: $nullable);
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

    public function getClass(): string
    {
        return $this->class;
    }

    public function getField(): string
    {
        return $this->field;
    }
}
