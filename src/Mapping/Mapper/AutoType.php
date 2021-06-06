<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

/**
 * @Annotation
 */
class AutoType implements RestApiBundle\Mapping\Mapper\TypeInterface
{
    public function getIsNullable(): bool
    {
        return false;
    }
}
