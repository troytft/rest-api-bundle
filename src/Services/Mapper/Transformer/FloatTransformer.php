<?php

declare(strict_types=1);

namespace RestApiBundle\Services\Mapper\Transformer;

use RestApiBundle;

class FloatTransformer implements TransformerInterface
{
    public function transform($value, array $options = []): float
    {
        if (!\is_numeric($value)) {
            throw new RestApiBundle\Exception\Mapper\Transformer\FloatRequiredException();
        }

        $value = \filter_var($value, FILTER_VALIDATE_FLOAT);
        if (false === $value) {
            throw new RestApiBundle\Exception\Mapper\Transformer\FloatRequiredException();
        }

        return $value;
    }
}
