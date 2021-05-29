<?php

namespace RestApiBundle\Mapping\RequestModel;

use RestApiBundle;

/**
 * @Annotation
 */
class ArrayType implements RestApiBundle\Mapping\Mapper\CollectionTypeInterface
{
    use NullableTrait;

    /**
     * Type hint forced to object, cause annotation reader doesn't support interfaces
     *
     * @var object
     */
    public $type;

    public function getType(): \RestApiBundle\Mapping\Mapper\TypeInterface
    {
        if (!$this->type instanceof RestApiBundle\Mapping\Mapper\TypeInterface) {
            throw new \InvalidArgumentException();
        }

        return $this->type;
    }

    public function getTransformerClass(): ?string
    {
        return null;
    }

    public function getTransformerOptions(): array
    {
        return [];
    }
}
