<?php

namespace RestApiBundle\Enum\OpenApi;

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
