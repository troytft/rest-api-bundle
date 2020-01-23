<?php

namespace RestApiBundle\DTO\Docs\ReturnType;

use RestApiBundle;

class NullType implements RestApiBundle\DTO\Docs\ReturnType\ReturnTypeInterface
{
    public function getIsNullPossible(): bool
    {
        return true;
    }
}
