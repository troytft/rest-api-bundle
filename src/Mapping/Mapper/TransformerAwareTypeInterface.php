<?php

namespace RestApiBundle\Mapping\Mapper;

interface TransformerAwareTypeInterface extends TypeInterface
{
    public function getTransformerClass(): string;
    public function getTransformerOptions(): array;
}
