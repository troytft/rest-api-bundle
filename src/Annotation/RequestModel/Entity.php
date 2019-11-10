<?php

namespace RestApiBundle\Annotation\RequestModel;

use Mapper\Annotation\NullableTrait;
use Mapper\DTO\Mapping\ScalarTypeInterface;
use RestApiBundle\Manager\RequestModel\EntityTransformer;

/**
 * @Annotation
 */
class Entity implements ScalarTypeInterface
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

    public function getTransformerName(): string
    {
        return EntityTransformer::getName();
    }

    public function getTransformerOptions(): array
    {
        return [
            EntityTransformer::CLASS_OPTION => $this->class,
            EntityTransformer::FIELD_OPTION => $this->field,
        ];
    }
}
