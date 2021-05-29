<?php

namespace RestApiBundle\Services\Mapper\Transformer;

use RestApiBundle;

use function filter_var;
use function is_numeric;

class FloatTransformer implements TransformerInterface
{
    public static function getName(): string
    {
        return static::class;
    }

    public function transform($value, array $options = [])
    {
        if (!is_numeric($value)) {
            throw new RestApiBundle\Exception\Mapper\Transformer\FloatRequiredException();
        }

        $value = filter_var($value, FILTER_VALIDATE_FLOAT);
        if ($value === false) {
            throw new RestApiBundle\Exception\Mapper\Transformer\FloatRequiredException();
        }

        return $value;
    }
}
