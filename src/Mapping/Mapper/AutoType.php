<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

/**
 * @Annotation
 */
class AutoType implements RestApiBundle\Mapping\Mapper\TypeInterface
{
    public function getTransformerClass(): ?string
    {
        return null;
    }

    public function getTransformerOptions(): array
    {
        return [];
    }

    public function getNullable(): ?bool
    {
        return null;
    }
}
