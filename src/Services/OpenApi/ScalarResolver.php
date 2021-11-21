<?php

namespace RestApiBundle\Services\OpenApi;

use Symfony\Component\PropertyInfo;
use cebe\openapi\spec as OpenApi;

class ScalarResolver
{
    public function resolve(string $type, bool $nullable): OpenApi\Schema
    {
        return match ($type) {
            PropertyInfo\Type::BUILTIN_TYPE_STRING => new OpenApi\Schema([
                'type' => OpenApi\Type::STRING,
                'nullable' => $nullable,
            ]),
            PropertyInfo\Type::BUILTIN_TYPE_INT => new OpenApi\Schema([
                'type' => OpenApi\Type::INTEGER,
                'nullable' => $nullable,
            ]),
            PropertyInfo\Type::BUILTIN_TYPE_FLOAT => new OpenApi\Schema([
                'type' => OpenApi\Type::NUMBER,
                'format' => 'double',
                'nullable' => $nullable,
            ]),
            PropertyInfo\Type::BUILTIN_TYPE_BOOL => new OpenApi\Schema([
                'type' => OpenApi\Type::BOOLEAN,
                'nullable' => $nullable,
            ]),
            default => throw new \InvalidArgumentException(),
        };
    }
}
