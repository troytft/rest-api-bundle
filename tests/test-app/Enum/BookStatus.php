<?php

namespace TestApp\Enum;

use RestApiBundle\Enum\Response\BaseSerializableEnum;

class BookStatus extends BaseSerializableEnum
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
