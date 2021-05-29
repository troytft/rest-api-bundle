<?php

namespace RestApiBundle\Services\Mapper\Transformer;

use RestApiBundle;

use function is_bool;
use function is_string;

class BooleanTransformer implements TransformerInterface
{
    public function transform($value, array $options = [])
    {
        if (!is_bool($value) && !is_string($value)) {
            throw new RestApiBundle\Exception\Mapper\Transformer\BooleanRequiredException();
        }

        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if (!is_bool($value)) {
            throw new RestApiBundle\Exception\Mapper\Transformer\BooleanRequiredException();
        }

        return $value;
    }
}
