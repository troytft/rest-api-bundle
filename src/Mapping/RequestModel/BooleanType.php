<?php

namespace RestApiBundle\Mapping\RequestModel;

use RestApiBundle;

/**
 * @Annotation
 */
class BooleanType implements RestApiBundle\Mapping\Mapper\ScalarTypeInterface
{
    use NullableTrait;

    public function getTransformerClass(): string
    {
        return RestApiBundle\Services\Mapper\Transformer\BooleanTransformer::class;
    }

    public function getTransformerOptions(): array
    {
        return [];
    }
}
