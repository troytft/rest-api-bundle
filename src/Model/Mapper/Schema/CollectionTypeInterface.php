<?php

namespace RestApiBundle\Model\Mapper\Schema;

interface CollectionTypeInterface extends TypeInterface
{
    public function getValuesType(): TypeInterface;
}
