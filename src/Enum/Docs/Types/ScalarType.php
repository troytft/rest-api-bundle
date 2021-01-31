<?php

namespace RestApiBundle\Enum\Docs\Types;

class ScalarType
{
    public const STRING = 'string';
    public const INT = 'int';
    public const INTEGER = 'integer';
    public const BOOL = 'bool';
    public const BOOLEAN = 'boolean';
    public const FLOAT = 'float';

    /**
     * @return string[]
     */
    public static function getValues(): array
    {
        return [
            static::STRING,
            static::INT,
            static::INTEGER,
            static::BOOL,
            static::BOOLEAN,
            static::FLOAT,
        ];
    }
}
