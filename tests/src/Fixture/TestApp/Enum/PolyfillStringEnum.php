<?php

namespace Tests\Fixture\TestApp\Enum;

class PolyfillStringEnum extends \RestApiBundle\Mapping\ResponseModel\BaseEnum
{
    public const CREATED = 'created';
    public const PUBLISHED = 'published';
    public const ARCHIVED = 'archived';


    final private function __construct(private int|string $value)
    {
    }

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

    public function getValue(): int|string
    {
        return $this->value;
    }

    public static function from(int|string $value): static
    {
        return new static($value);
    }

    public static function tryFrom(int|string $value): ?static
    {
        if (!in_array($value, static::getValues(), true)) {
            return null;
        }

        return new static($value);
    }
}
