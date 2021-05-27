<?php

namespace RestApiBundle\Mapping\Mapper;

interface TypeInterface
{
    public function getNullable(): ?bool;
    public function getTransformerName(): ?string;
    public function getTransformerOptions(): array;
}
