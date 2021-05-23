<?php

namespace RestApiBundle\Services\OpenApi;

use RestApiBundle;
use Symfony\Component\PropertyInfo;
use cebe\openapi\spec as OpenApi;

abstract class AbstractSchemaResolver
{
    protected function resolveScalarTypeOld(RestApiBundle\Model\OpenApi\Types\ScalarInterface $scalarType): OpenApi\Schema
    {
        switch (true) {
            case $scalarType instanceof RestApiBundle\Model\OpenApi\Types\StringType:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::STRING,
                    'nullable' => $scalarType->getNullable(),
                ]);

                break;

            case $scalarType instanceof RestApiBundle\Model\OpenApi\Types\IntegerType:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::INTEGER,
                    'nullable' => $scalarType->getNullable(),
                ]);

                break;

            case $scalarType instanceof RestApiBundle\Model\OpenApi\Types\FloatType:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::NUMBER,
                    'format' => 'double',
                    'nullable' => $scalarType->getNullable(),
                ]);

                break;

            case $scalarType instanceof RestApiBundle\Model\OpenApi\Types\BooleanType:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::BOOLEAN,
                    'nullable' => $scalarType->getNullable(),
                ]);

                break;

            default:
                throw new \InvalidArgumentException();
        }

        return $result;
    }

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
