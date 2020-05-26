<?php

namespace RestApiBundle\Annotation\Request;

use RestApiBundle;
use Mapper\Annotation\NullableTrait;

/**
 * @Annotation
 */
class ArrayOfEntitiesType extends RestApiBundle\Annotation\Request\ArrayType
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
        return RestApiBundle\Services\Request\MapperTransformer\EntitiesCollectionTransformer::getName();
    }

    public function getTransformerOptions(): array
    {
        return [
            RestApiBundle\Services\Request\MapperTransformer\EntitiesCollectionTransformer::CLASS_OPTION => $this->class,
            RestApiBundle\Services\Request\MapperTransformer\EntitiesCollectionTransformer::FIELD_OPTION => $this->field,
        ];
    }
}
