<?php

namespace RestApiBundle\Enum\Docs;

class Format
{
    public const YAML = 'yaml';
    public const JSON = 'json';

    /**
     * @return string[]
     */
    public static function getValues(): array
    {
        return [
            static::YAML,
            static::JSON,
        ];
    }
}
