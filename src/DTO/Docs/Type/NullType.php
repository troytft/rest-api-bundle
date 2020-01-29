<?php

namespace RestApiBundle\DTO\Docs\Type;

use RestApiBundle;

class NullType implements RestApiBundle\DTO\Docs\Type\TypeInterface
{
    public function getNullable(): bool
    {
        return true;
    }
}
