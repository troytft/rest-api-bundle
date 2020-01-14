<?php

namespace RestApiBundle\Annotation\Request;

use RestApiBundle;
use Mapper\Annotation\NullableTrait;
use Mapper\DTO\Mapping\CollectionTypeInterface;
use Mapper\DTO\Mapping\TypeInterface;

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
        return RestApiBundle\Services\Request\Mapper\EntitiesCollectionTransformer::getName();
    }

    public function getTransformerOptions(): array
    {
        return [
            RestApiBundle\Services\Request\Mapper\EntitiesCollectionTransformer::CLASS_OPTION => $this->class,
            RestApiBundle\Services\Request\Mapper\EntitiesCollectionTransformer::FIELD_OPTION => $this->field,
        ];
    }
}
