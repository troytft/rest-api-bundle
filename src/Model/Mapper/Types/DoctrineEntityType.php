<?php

namespace RestApiBundle\Model\Mapper\Types;

use RestApiBundle;

class DoctrineEntityType extends RestApiBundle\Model\Mapper\Types\BaseNullableType implements RestApiBundle\Model\Mapper\Types\TransformerAwareTypeInterface
{
    public function __construct(
        private string $class = '',
        public string $field = 'id',
        ?bool $nullable = null
    ) {
        parent::__construct(nullable: $nullable);
    }

    public function getTransformerClass(): string
    {
        return RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::class;
    }

    public function getTransformerOptions(): array
    {
        return [
            RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::CLASS_OPTION => $this->class,
            RestApiBundle\Services\Mapper\Transformer\DoctrineEntityTransformer::FIELD_OPTION => $this->field,
        ];
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getField(): string
    {
        return $this->field;
    }
}
