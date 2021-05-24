<?php

namespace RestApiBundle\Mapping\RequestModel;

use RestApiBundle;
use Mapper\Annotation\NullableTrait;
use Mapper\DTO\Mapping\ScalarTypeInterface;

/**
 * @Annotation
 */
class EntityType implements ScalarTypeInterface
{
    use NullableTrait;

    public string $class;
    public string $field = 'id';

    public function getTransformerName(): string
    {
        return RestApiBundle\Services\RequestModel\MapperTransformer\EntityTransformer::getName();
    }

    public function getTransformerOptions(): array
    {
        return [
            RestApiBundle\Services\RequestModel\MapperTransformer\EntityTransformer::CLASS_OPTION => $this->class,
            RestApiBundle\Services\RequestModel\MapperTransformer\EntityTransformer::FIELD_OPTION => $this->field,
        ];
    }
}
