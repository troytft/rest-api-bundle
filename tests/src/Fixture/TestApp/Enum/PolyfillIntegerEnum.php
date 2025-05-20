<?php
declare(strict_types=1);

namespace Tests\Fixture\TestApp\Enum;

class PolyfillIntegerEnum extends \RestApiBundle\Mapping\ResponseModel\BaseEnum
{
    public const CREATED = 0;
    public const PUBLISHED = 1;
    public const ARCHIVED = 2;

    /**
     * @return int[]
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
