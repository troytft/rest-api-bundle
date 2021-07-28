<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class AutoType implements RestApiBundle\Mapping\Mapper\TypeInterface
{
    public function getIsNullable(): bool
    {
        return false;
    }
}
