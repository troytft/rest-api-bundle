<?php

namespace RestApiBundle\Services\OpenApi;

use Symfony\Component\PropertyInfo;
use cebe\openapi\spec as OpenApi;

abstract class AbstractSchemaResolver
{
    protected function resolveScalarType(string $type, bool $nullable): OpenApi\Schema
    {
        switch ($type) {
            case PropertyInfo\Type::BUILTIN_TYPE_STRING:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::STRING,
                    'nullable' => $nullable,
                ]);

                break;

            case PropertyInfo\Type::BUILTIN_TYPE_INT:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::INTEGER,
                    'nullable' => $nullable,
                ]);

                break;

            case PropertyInfo\Type::BUILTIN_TYPE_FLOAT:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::NUMBER,
                    'format' => 'double',
                    'nullable' => $nullable,
                ]);

                break;

            case PropertyInfo\Type::BUILTIN_TYPE_BOOL:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::BOOLEAN,
                    'nullable' => $nullable,
                ]);

                break;

            default:
                throw new \InvalidArgumentException();
        }

        return $result;
    }
}
