<?php

declare(strict_types=1);

namespace RestApiBundle\Services\Mapper\Transformer;

use RestApiBundle;

class BooleanTransformer implements TransformerInterface
{
    public function transform($value, array $options = []): bool
    {
        if (\is_string($value)) {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if (!\is_bool($value)) {
                throw new RestApiBundle\Exception\Mapper\Transformer\BooleanRequiredException();
            }
        } elseif (0 === $value) {
            $value = false;
        } elseif (1 === $value) {
            $value = true;
        } elseif (!\is_bool($value)) {
            throw new RestApiBundle\Exception\Mapper\Transformer\BooleanRequiredException();
        }

        return $value;
    }
}
