<?php

namespace RestApiBundle\Mapping\RequestModel;

use RestApiBundle;

/**
 * @Annotation
 */
class EntityType implements RestApiBundle\Mapping\Mapper\ScalarTypeInterface
{
    use NullableTrait;

    public string $class;
    public string $field = 'id';

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
}
