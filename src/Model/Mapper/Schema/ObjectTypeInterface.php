<?php

namespace RestApiBundle\Model\Mapper\Schema;

interface ObjectTypeInterface extends TypeInterface
{
    public function getClass(): string;

    /**
     * @return TypeInterface[]
     */
    public function getProperties(): array;
}
