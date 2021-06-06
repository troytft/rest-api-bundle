<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

/**
 * @Annotation
 */
class TimestampType implements RestApiBundle\Mapping\Mapper\TransformerAwareTypeInterface
{
    use NullableTrait;

    public function getTransformerClass(): string
    {
        return RestApiBundle\Services\Mapper\Transformer\TimestampTransformer::class;
    }

    public function getTransformerOptions(): array
    {
        return [];
    }
}
