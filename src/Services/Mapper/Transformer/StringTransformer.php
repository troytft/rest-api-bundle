<?php

declare(strict_types=1);

namespace RestApiBundle\Services\Mapper\Transformer;

use RestApiBundle;

class StringTransformer implements TransformerInterface
{
    public const TRIM_OPTION = 'trim';
    public const EMPTY_TO_NULL_OPTION = 'emptyToNull';

    /**
     * @param array<string, mixed> $options
     */
    public function transform(mixed $value, array $options = []): ?string
    {
        if (\is_numeric($value)) {
            $value = (string) $value;
        } elseif (!\is_string($value)) {
            throw new RestApiBundle\Exception\Mapper\Transformer\StringRequiredException();
        }

        $trim = $options[static::TRIM_OPTION] ?? false;
        if ($trim) {
            $value = \trim($value);
        }

        $emptyToNull = $options[static::EMPTY_TO_NULL_OPTION] ?? false;
        if ($emptyToNull && $value === '') {
            $value = null;
        }

        return $value;
    }
}
