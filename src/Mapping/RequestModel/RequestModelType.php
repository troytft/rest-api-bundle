<?php

namespace RestApiBundle\Mapping\RequestModel;

use RestApiBundle;

/**
 * @Annotation
 */
class RequestModelType implements RestApiBundle\Mapping\Mapper\ObjectTypeInterface
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
