<?php

namespace Tests\Fixture\TestApp\Enum;

class BookStatus extends \RestApiBundle\Mapping\ResponseModel\BaseEnum
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
