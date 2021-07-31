<?php

namespace RestApiBundle\Mapping\Mapper;

interface NullableAwareTypeInterface extends TypeInterface
{
    public function getIsNullable(): ?bool;
    public function setIsNullable(?bool $value): static;
}
