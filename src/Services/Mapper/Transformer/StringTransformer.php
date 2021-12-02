<?php

namespace RestApiBundle\Services\Mapper\Transformer;

use RestApiBundle;

use function is_string;

class StringTransformer implements TransformerInterface
{
    public function transform($value, array $options = [])
    {
        if (is_numeric($value)) {
            $value = (string) $value;
        } elseif (!is_string($value)) {
            throw new RestApiBundle\Exception\Mapper\Transformer\StringRequiredException();
        }

        return $value;
    }
}
