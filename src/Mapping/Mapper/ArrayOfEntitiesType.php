<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

/**
 * @Annotation
 */
class ArrayOfEntitiesType extends RestApiBundle\Mapping\Mapper\ArrayType
{
    use NullableTrait;

    public string $class;
    public string $field = 'id';

    public function __construct()
    {
        $this->type = new IntegerType();
    }

    public function getTransformerClass(): ?string
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
