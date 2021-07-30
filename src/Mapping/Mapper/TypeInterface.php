<?php

namespace RestApiBundle\Mapping\Mapper;

interface TypeInterface
{
    public function getIsNullable(): ?bool;
    public function setIsNullable(?bool $value);
}
