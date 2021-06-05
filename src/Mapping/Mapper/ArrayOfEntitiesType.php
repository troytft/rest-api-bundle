<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

/**
 * @Annotation
 */
class ArrayOfEntitiesType implements RestApiBundle\Mapping\Mapper\TransformerAwareTypeInterface
{
    use NullableTrait;

    public string $class;
    public string $field = 'id';

    public function getTransformerClass(): string
    {
        return RestApiBundle\Services\Mapper\Transformer\EntitiesCollectionTransformer::class;
    }

    public function getTransformerOptions(): array
    {
        return [
            RestApiBundle\Services\Mapper\Transformer\EntitiesCollectionTransformer::CLASS_OPTION => $this->class,
            RestApiBundle\Services\Mapper\Transformer\EntitiesCollectionTransformer::FIELD_OPTION => $this->field,
        ];
    }
}
