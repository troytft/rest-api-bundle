<?php

namespace RestApiBundle\Services\Mapper\Transformer;

use RestApiBundle;

use function is_string;
use function trim;

class StringTransformer implements TransformerInterface
{
    public const TRIM_OPTION = 'trim';

    public function transform($value, array $options = [])
    {
        if (!is_string($value)) {
            throw new RestApiBundle\Exception\Mapper\Transformer\StringRequiredException();
        }

        if ($options[static::TRIM_OPTION] ?? false) {
            $value = trim($value);
        }

        return $value;
    }
}
