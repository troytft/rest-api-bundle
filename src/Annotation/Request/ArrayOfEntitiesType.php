<?php

namespace RestApiBundle\Annotation\Request;

use RestApiBundle;
use Mapper\Annotation\NullableTrait;
use Mapper\DTO\Mapping\CollectionTypeInterface;
use Mapper\DTO\Mapping\TypeInterface;

/**
 * @Annotation
 */
class ArrayOfEntitiesType implements CollectionTypeInterface
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

    /**
     * @var TypeInterface
     */
    public $type;

    public function __construct()
    {
        $this->type = new IntegerType();
    }

    public function getType(): TypeInterface
    {
        return $this->type;
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
