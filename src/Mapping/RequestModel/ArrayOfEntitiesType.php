<?php

namespace RestApiBundle\Mapping\RequestModel;

use RestApiBundle;
use Mapper\Annotation\NullableTrait;

/**
 * @Annotation
 */
class ArrayOfEntitiesType extends RestApiBundle\Mapping\RequestModel\ArrayType
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
