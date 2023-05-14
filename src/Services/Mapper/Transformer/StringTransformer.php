<?php

namespace RestApiBundle\Services\Mapper\Transformer;

use RestApiBundle;

use function is_string;

class StringTransformer implements TransformerInterface
{
    public const TRIM_OPTION = 'trim';

    public function transform($value, array $options = []): string
    {
        if (is_numeric($value)) {
            $value = (string) $value;
        } elseif (!is_string($value)) {
            throw new RestApiBundle\Exception\Mapper\Transformer\StringRequiredException();
        }

        $trim = $options[static::TRIM_OPTION] ?? false;
        if ($trim) {
            $value = trim($value);
        }

        return $value;
    }
}
