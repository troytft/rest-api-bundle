<?php

namespace RestApiBundle\DTO\OpenApi\Schema;

interface SchemaTypeInterface
{
    public function getNullable(): bool;
}
