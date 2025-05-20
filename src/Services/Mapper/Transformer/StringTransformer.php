<?php

declare(strict_types=1);

namespace RestApiBundle\Services\Mapper\Transformer;

use RestApiBundle;

use function is_string;

class StringTransformer implements TransformerInterface
{
    public const TRIM_OPTION = 'trim';
    public const EMPTY_TO_NULL_OPTION = 'emptyToNull';

    public function transform($value, array $options = []): ?string
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

        $emptyToNull = $options[static::EMPTY_TO_NULL_OPTION] ?? false;
        if ($emptyToNull && empty($value)) {
            $value = null;
        }

        return $value;
    }
}
