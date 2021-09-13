<?php

namespace RestApiBundle\Model\Mapper\Types;

interface NullableAwareTypeInterface extends TypeInterface
{
    public function getIsNullable(): ?bool;
    public function setIsNullable(?bool $value): static;
}
