<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

/**
 * @Annotation
 */
class IntegerType implements RestApiBundle\Mapping\Mapper\TransformerAwareTypeInterface
{
    use NullableTrait;

    public function getTransformerClass(): string
    {
        return RestApiBundle\Services\Mapper\Transformer\IntegerTransformer::class;
    }

    public function getTransformerOptions(): array
    {
        return [];
    }
}
