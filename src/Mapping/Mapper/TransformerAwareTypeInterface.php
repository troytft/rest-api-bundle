<?php

namespace RestApiBundle\Mapping\Mapper;

interface TransformerAwareTypeInterface extends NullableAwareTypeInterface
{
    public function getTransformerClass(): string;
    public function getTransformerOptions(): array;
}
