<?php

namespace Tests\Fixture\Helper\TypeExtractorTest;

class IntegerEnumAsClassWithValues
{
    public const VALUE_5 = 5;
    public const VALUE_10 = 10;
    public const VALUE_100 = 100;

    /**
     * @return int[]
     */
    public static function getValues(): array
    {
        return [
            static::VALUE_10,
            static::VALUE_100,
        ];
    }
}
