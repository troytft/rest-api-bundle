<?php

namespace RestApiBundle\DTO\OpenApi\Types;

use RestApiBundle;

class NullType implements RestApiBundle\DTO\OpenApi\Types\TypeInterface
{
    public function getNullable(): bool
    {
        return true;
    }
}
