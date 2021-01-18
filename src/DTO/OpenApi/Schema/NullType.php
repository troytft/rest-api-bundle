<?php

namespace RestApiBundle\DTO\OpenApi\Schema;

use RestApiBundle;

class NullType implements
    RestApiBundle\DTO\OpenApi\Schema\SchemaTypeInterface,
    RestApiBundle\DTO\OpenApi\ResponseInterface
{
    public function getNullable(): bool
    {
        return true;
    }
}
