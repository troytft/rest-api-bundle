<?php

namespace TestApp\Enum;

use RestApiBundle\Enum\Response\BaseSerializableEnum;

class StringEnum extends BaseSerializableEnum
{
    public const FIRST = 'first';
    public const SECOND = 'second';
    public const THIRD = 'third';

    /**
     * {@inheritDoc}
     */
    public function getValues(): array
    {
        return [
            static::FIRST,
            static::SECOND,
            static::THIRD,
        ];
    }
}
