<?php

namespace RestApiBundle\Mapping\RequestModel;

use RestApiBundle;

/**
 * @Annotation
 */
class StringType implements RestApiBundle\Mapping\Mapper\ScalarTypeInterface
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
