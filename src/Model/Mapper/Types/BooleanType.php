<?php

namespace RestApiBundle\Model\Mapper\Types;

use RestApiBundle;

class BooleanType extends BaseNullableType implements RestApiBundle\Model\Mapper\Types\TransformerAwareTypeInterface
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
