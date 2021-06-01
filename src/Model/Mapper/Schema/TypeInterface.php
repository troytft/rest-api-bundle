<?php

namespace RestApiBundle\Model\Mapper\Schema;

interface TypeInterface
{
    public function getNullable(): bool;
    public function getTransformerClass(): ?string;
    public function getTransformerOptions(): array;
    public function getSetterName(): ?string;
    public function setSetterName(string $value);
}
