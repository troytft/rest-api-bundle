<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

class BooleanType extends BaseNullableType implements RestApiBundle\Mapping\Mapper\TransformerAwareTypeInterface
{
    public function getTransformerClass(): string
    {
        return RestApiBundle\Services\Mapper\Transformer\BooleanTransformer::class;
    }

    public function getTransformerOptions(): array
    {
        return [];
    }
}
