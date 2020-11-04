<?php

namespace RestApiBundle\DTO\OpenApi\Schema;

use RestApiBundle;

class NullType implements RestApiBundle\DTO\OpenApi\Schema\SchemaTypeInterface
{
    public function getNullable(): bool
    {
        return true;
    }
}
