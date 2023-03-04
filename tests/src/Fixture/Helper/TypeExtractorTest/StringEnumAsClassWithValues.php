<?php

namespace Tests\Fixture\Helper\TypeExtractorTest;

class StringEnumAsClassWithValues
{
    public const VALUE_5 = 'value_5';
    public const VALUE_10 = 'value_10';
    public const VALUE_100 = 'value_100';

    /**
     * @return string[]
     */
    public static function getValues(): array
    {
        return [
            static::VALUE_10,
            static::VALUE_100,
        ];
    }
}
