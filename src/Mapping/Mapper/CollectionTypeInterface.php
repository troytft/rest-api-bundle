<?php

namespace RestApiBundle\Mapping\Mapper;

interface CollectionTypeInterface extends TypeInterface
{
    public function getType(): TypeInterface;
}
