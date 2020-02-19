<?php

namespace RestApiBundle\DTO\Docs\Schema;

use RestApiBundle;

class NullType implements RestApiBundle\DTO\Docs\Schema\TypeInterface
{
    public function getNullable(): bool
    {
        return true;
    }
}
