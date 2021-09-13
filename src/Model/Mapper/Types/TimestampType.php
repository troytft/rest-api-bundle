<?php

namespace RestApiBundle\Model\Mapper\Types;

use RestApiBundle;

class TimestampType extends RestApiBundle\Model\Mapper\Types\BaseNullableType implements RestApiBundle\Model\Mapper\Types\TransformerAwareTypeInterface
{
    public function getTransformerClass(): string
    {
        return RestApiBundle\Services\Mapper\Transformer\TimestampTransformer::class;
    }

    public function getTransformerOptions(): array
    {
        return [];
    }
}
