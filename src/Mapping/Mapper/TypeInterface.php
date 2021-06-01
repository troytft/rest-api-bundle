<?php

namespace RestApiBundle\Mapping\Mapper;

interface TypeInterface
{
    public function getNullable(): ?bool;
    public function getTransformerClass(): ?string;
    public function getTransformerOptions(): array;
}
