<?php

namespace RestApiBundle\Mapping\Mapper;

interface CollectionTypeInterface extends TypeInterface
{
    public function getValueType(): TypeInterface;
}
