<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

class FloatType extends RestApiBundle\Mapping\Mapper\BaseNullableType implements RestApiBundle\Mapping\Mapper\TransformerAwareTypeInterface
{
    public function getTransformerClass(): string
    {
        return RestApiBundle\Services\Mapper\Transformer\FloatTransformer::class;
    }

    public function getTransformerOptions(): array
    {
        return [];
    }
}
