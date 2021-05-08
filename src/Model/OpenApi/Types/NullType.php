<?php

namespace RestApiBundle\Model\OpenApi\Types;

use RestApiBundle;

class NullType implements RestApiBundle\Model\OpenApi\Types\TypeInterface
{
    public function getNullable(): bool
    {
        return true;
    }
}
