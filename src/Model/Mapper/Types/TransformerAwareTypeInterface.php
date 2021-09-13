<?php

namespace RestApiBundle\Model\Mapper\Types;

interface TransformerAwareTypeInterface extends NullableAwareTypeInterface
{
    public function getTransformerClass(): string;
    public function getTransformerOptions(): array;
}
