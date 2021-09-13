<?php

namespace RestApiBundle\Model\Mapper\Types;

use RestApiBundle;

class IntegerType extends RestApiBundle\Model\Mapper\Types\BaseNullableType implements RestApiBundle\Model\Mapper\Types\TransformerAwareTypeInterface
{
    public function getTransformerClass(): string
    {
        return RestApiBundle\Services\Mapper\Transformer\IntegerTransformer::class;
    }

    public function getTransformerOptions(): array
    {
        return [];
    }
}
