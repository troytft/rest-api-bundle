<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class ExposeAll implements RestApiBundle\Mapping\Mapper\TypeInterface
{
    public function getIsNullable(): ?bool
    {
        return null;
    }

    public function setIsNullable(?bool $value)
    {
    }
}
