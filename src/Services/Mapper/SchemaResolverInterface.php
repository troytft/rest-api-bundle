<?php

declare(strict_types=1);

namespace RestApiBundle\Services\Mapper;

use RestApiBundle;

interface SchemaResolverInterface
{
    public function resolve(string $class, bool $isNullable = false): RestApiBundle\Model\Mapper\Schema;
}
