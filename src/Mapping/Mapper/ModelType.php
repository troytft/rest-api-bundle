<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

/**
 * @Annotation
 */
class ModelType implements RestApiBundle\Mapping\Mapper\TypeInterface
{
    use NullableTrait;

    public string $class;
}
