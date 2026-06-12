<?php

declare(strict_types=1);

namespace RestApiBundle\Services\Mapper\Transformer;

use RestApiBundle;

class EnumTransformer implements TransformerInterface
{
    public const CLASS_OPTION = 'class';

    /**
     * @param array<string, mixed> $options
     */
    public function transform(mixed $value, array $options): mixed
    {
        $class = $options[static::CLASS_OPTION] ?? throw new \InvalidArgumentException();
        $result = $class::tryFrom($value);
        if (!$result) {
            throw new RestApiBundle\Exception\Mapper\Transformer\ValueNotFoundInEnumException();
        }

        return $result;
    }
}
