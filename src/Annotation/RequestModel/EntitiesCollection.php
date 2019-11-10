<?php

namespace RestApiBundle\Annotation\RequestModel;

use Mapper\Annotation\NullableTrait;
use Mapper\DTO\Mapping\CollectionTypeInterface;
use Mapper\DTO\Mapping\TypeInterface;
use RestApiBundle\Manager\RequestModel\Transformer\EntitiesCollectionTransformer;

/**
 * @Annotation
 */
class EntitiesCollection implements CollectionTypeInterface
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
        return EntitiesCollectionTransformer::getName();
    }

    public function getTransformerOptions(): array
    {
        return [
            EntitiesCollectionTransformer::CLASS_OPTION => $this->class,
            EntitiesCollectionTransformer::FIELD_OPTION => $this->field,
        ];
    }
}
