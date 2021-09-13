<?php

namespace RestApiBundle\Model\Mapper\Types;

use RestApiBundle;

class FloatType extends RestApiBundle\Model\Mapper\Types\BaseNullableType implements RestApiBundle\Model\Mapper\Types\TransformerAwareTypeInterface
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
