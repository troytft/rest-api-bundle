<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Field implements RestApiBundle\Mapping\Mapper\TypeInterface
{
    public function getIsNullable(): ?bool
    {
        return null;
    }

    public function setIsNullable(?bool $value)
    {
    }
}
