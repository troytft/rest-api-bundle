<?php

namespace RestApiBundle\DTO\Docs\Types;

use RestApiBundle;

class NullType implements RestApiBundle\DTO\Docs\Types\TypeInterface
{
    public function getNullable(): bool
    {
        return true;
    }
}
