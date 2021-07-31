<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

class StringType extends RestApiBundle\Mapping\Mapper\BaseNullableType implements RestApiBundle\Mapping\Mapper\TransformerAwareTypeInterface
{
    public function getTransformerClass(): string
    {
        return RestApiBundle\Services\Mapper\Transformer\StringTransformer::class;
    }

    public function getTransformerOptions(): array
    {
        return [];
    }
}
