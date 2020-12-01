<?php

namespace RestApiBundle\DTO\OpenApi\Schema;

interface TypeInterface
{
    public function getNullable(): bool;
}
