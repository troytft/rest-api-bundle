<?php

namespace RestApiBundle\Mapping\ResponseModel;

use RestApiBundle;

abstract class BaseEnum implements RestApiBundle\Mapping\ResponseModel\EnumInterface, RestApiBundle\Mapping\Mapper\EnumInterface
{
    final private function __construct(private int|string $value)
    {
    }

    public static function from(int|string $value): static
    {
        return new static($value);
    }

    public function getValue(): int|string
    {
        return $this->value;
    }
}
