<?php

namespace RestApiBundle\Mapping\Mapper;

interface EnumInterface
{
    public function getValue(): int|string;
    public static function from(int|string $value): static;
}
