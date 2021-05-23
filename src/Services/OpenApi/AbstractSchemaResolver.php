<?php

namespace RestApiBundle\Services\OpenApi;

use Symfony\Component\PropertyInfo;
use cebe\openapi\spec as OpenApi;

abstract class AbstractSchemaResolver
{
    protected function resolveScalarType(PropertyInfo\Type $type): OpenApi\Schema
    {
        switch ($type->getBuiltinType()) {
            case PropertyInfo\Type::BUILTIN_TYPE_STRING:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::STRING,
                    'nullable' => $type->isNullable(),
                ]);

                break;

            case PropertyInfo\Type::BUILTIN_TYPE_INT:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::INTEGER,
                    'nullable' => $type->isNullable(),
                ]);

                break;

            case PropertyInfo\Type::BUILTIN_TYPE_FLOAT:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::NUMBER,
                    'format' => 'double',
                    'nullable' => $type->isNullable(),
                ]);

                break;

            case PropertyInfo\Type::BUILTIN_TYPE_BOOL:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::BOOLEAN,
                    'nullable' => $type->isNullable(),
                ]);

                break;

            default:
                throw new \InvalidArgumentException();
        }

        return $result;
    }
}
