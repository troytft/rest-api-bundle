<?php

namespace RestApiBundle\Services\OpenApi;

use RestApiBundle;
use cebe\openapi\spec as OpenApi;

abstract class AbstractSchemaResolver
{
    protected function resolveScalarType(RestApiBundle\Model\OpenApi\Types\ScalarInterface $scalarType): OpenApi\Schema
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
}
