<?php

namespace TestApp\Enum;

class Status
{
    public const CREATED = 'created';
    public const PUBLISHED = 'published';
    public const ARCHIVED = 'archived';

    /**
     * @return string[]
     */
    public static function getValues(): array
    {
        return [
            static::CREATED,
            static::PUBLISHED,
            static::ARCHIVED,
        ];
    }
}
