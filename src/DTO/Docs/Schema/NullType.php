<?php

namespace RestApiBundle\DTO\Docs\Schema;

use RestApiBundle;

class NullType implements RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
{
    public function getNullable(): bool
    {
        return true;
    }
}
