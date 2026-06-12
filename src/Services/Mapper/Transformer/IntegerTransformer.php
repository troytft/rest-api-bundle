<?php

declare(strict_types=1);

namespace RestApiBundle\Services\Mapper\Transformer;

use RestApiBundle;

class IntegerTransformer implements TransformerInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function transform(mixed $value, array $options = []): int
    {
        if (!\is_numeric($value)) {
            throw new RestApiBundle\Exception\Mapper\Transformer\IntegerRequiredException();
        }

        $value = \filter_var($value, \FILTER_VALIDATE_INT);
        if ($value === false) {
            throw new RestApiBundle\Exception\Mapper\Transformer\IntegerRequiredException();
        }

        return $value;
    }
}
