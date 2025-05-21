<?php

declare(strict_types=1);

namespace RestApiBundle\Mapping\ResponseModel;

use RestApiBundle;

abstract class BaseEnum implements EnumInterface, RestApiBundle\Mapping\Mapper\EnumInterface
{
    final private function __construct(private int|string $value)
    {
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

    abstract public static function getValues(): array;
}
