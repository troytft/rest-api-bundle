<?php

namespace RestApiBundle\Services\OpenApi\Schema;

use RestApiBundle;
use function in_array;
use function ltrim;

abstract class BaseReader
{
    protected function createScalarTypeFromString(string $type, bool $nullable): ?RestApiBundle\DTO\OpenApi\Types\ScalarInterface
    {
        switch ($type) {
            case RestApiBundle\Enum\Docs\ScalarType::STRING:
                $result = new RestApiBundle\DTO\OpenApi\Types\StringType($nullable);

                break;

            case RestApiBundle\Enum\Docs\ScalarType::INT:
            case RestApiBundle\Enum\Docs\ScalarType::INTEGER:
                $result = new RestApiBundle\DTO\OpenApi\Types\IntegerType($nullable);

                break;

            case RestApiBundle\Enum\Docs\ScalarType::FLOAT:
                $result = new RestApiBundle\DTO\OpenApi\Types\FloatType($nullable);

                break;

            case RestApiBundle\Enum\Docs\ScalarType::BOOL:
            case RestApiBundle\Enum\Docs\ScalarType::BOOLEAN:
                $result = new RestApiBundle\DTO\OpenApi\Types\BooleanType($nullable);

                break;

            default:
                throw new \InvalidArgumentException();
        }

        return $result;
    }

    protected function isScalarType(string $type): bool
    {
        return in_array($type, RestApiBundle\Enum\Docs\ScalarType::getValues(), true);
    }

    protected function createClassTypeFromString(string $class, bool $nullable): ?RestApiBundle\DTO\OpenApi\Types\ClassType
    {
        $class = ltrim((string) $class, '\\');

        return new RestApiBundle\DTO\OpenApi\Types\ClassType($class, $nullable);
    }
}
