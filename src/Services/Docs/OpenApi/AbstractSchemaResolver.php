<?php

namespace RestApiBundle\Services\Docs\OpenApi;

use RestApiBundle;
use cebe\openapi\spec as OpenApi;

abstract class AbstractSchemaResolver
{
    protected function convertScalarType(RestApiBundle\DTO\Docs\Types\ScalarInterface $scalarType): OpenApi\Schema
    {
        switch (true) {
            case $scalarType instanceof RestApiBundle\DTO\Docs\Types\StringType:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::STRING,
                    'nullable' => $scalarType->getNullable(),
                ]);

                break;

            case $scalarType instanceof RestApiBundle\DTO\Docs\Types\IntegerType:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::INTEGER,
                    'nullable' => $scalarType->getNullable(),
                ]);

                break;

            case $scalarType instanceof RestApiBundle\DTO\Docs\Types\FloatType:
                $result = new OpenApi\Schema([
                    'type' => OpenApi\Type::NUMBER,
                    'format' => 'double',
                    'nullable' => $scalarType->getNullable(),
                ]);

                break;

            case $scalarType instanceof RestApiBundle\DTO\Docs\Types\BooleanType:
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
