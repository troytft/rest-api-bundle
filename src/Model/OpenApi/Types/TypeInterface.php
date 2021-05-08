<?php

namespace RestApiBundle\Model\OpenApi\Types;

interface TypeInterface
{
    public function getNullable(): bool;
}
