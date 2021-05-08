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

    /**
     * @var string
     */
    public $class;

    /**
     * @var string
     */
    public $field = 'id';

    public function __construct()
    {
        $this->type = new IntegerType();
    }

    public function getTransformerName(): ?string
    {
        return RestApiBundle\Services\RequestModel\MapperTransformer\EntitiesCollectionTransformer::getName();
    }

    public function getTransformerOptions(): array
    {
        return [
            RestApiBundle\Services\RequestModel\MapperTransformer\EntitiesCollectionTransformer::CLASS_OPTION => $this->class,
            RestApiBundle\Services\RequestModel\MapperTransformer\EntitiesCollectionTransformer::FIELD_OPTION => $this->field,
        ];
    }
}
