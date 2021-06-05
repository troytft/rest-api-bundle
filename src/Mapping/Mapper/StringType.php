<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

/**
 * @Annotation
 */
class StringType implements RestApiBundle\Mapping\Mapper\TransformerAwareTypeInterface
{
    use NullableTrait;

    public function getTransformerClass(): string
    {
        return RestApiBundle\Services\Mapper\Transformer\StringTransformer::class;
    }

    public function getTransformerOptions(): array
    {
        return [];
    }
}
