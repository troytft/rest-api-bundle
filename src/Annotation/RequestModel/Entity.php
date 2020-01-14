<?php

namespace RestApiBundle\Annotation\RequestModel;

use RestApiBundle;
use Mapper\Annotation\NullableTrait;
use Mapper\DTO\Mapping\ScalarTypeInterface;

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
        return RestApiBundle\Services\Request\Mapper\EntityTransformer::getName();
    }

    public function getTransformerOptions(): array
    {
        return [
            RestApiBundle\Services\Request\Mapper\EntityTransformer::CLASS_OPTION => $this->class,
            RestApiBundle\Services\Request\Mapper\EntityTransformer::FIELD_OPTION => $this->field,
        ];
    }
}
