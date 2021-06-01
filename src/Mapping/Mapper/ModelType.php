<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

/**
 * @Annotation
 */
class ModelType implements RestApiBundle\Mapping\Mapper\ObjectTypeInterface
{
    use NullableTrait;

    public string $class;

    public function getClassName(): string
    {
        return $this->class;
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
