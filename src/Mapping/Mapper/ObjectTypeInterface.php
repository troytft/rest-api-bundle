<?php

namespace RestApiBundle\Mapping\Mapper;

interface ObjectTypeInterface extends TypeInterface
{
    public function getClassName(): string;
}
