<?php

namespace RestApiBundle\Services\OpenApi\Reader;

use RestApiBundle;
use function in_array;
use function ltrim;

abstract class BaseReader
{
    protected function createScalarTypeFromString(string $type, bool $nullable): ?RestApiBundle\DTO\OpenApi\Schema\ScalarInterface
    {
        switch ($type) {
            case RestApiBundle\Enum\Docs\ScalarType::STRING:
                $result = new RestApiBundle\DTO\OpenApi\Schema\StringType($nullable);

                break;

            case RestApiBundle\Enum\Docs\ScalarType::INT:
            case RestApiBundle\Enum\Docs\ScalarType::INTEGER:
                $result = new RestApiBundle\DTO\OpenApi\Schema\IntegerType($nullable);

                break;

            case RestApiBundle\Enum\Docs\ScalarType::FLOAT:
                $result = new RestApiBundle\DTO\OpenApi\Schema\FloatType($nullable);

                break;

            case RestApiBundle\Enum\Docs\ScalarType::BOOL:
            case RestApiBundle\Enum\Docs\ScalarType::BOOLEAN:
                $result = new RestApiBundle\DTO\OpenApi\Schema\BooleanType($nullable);

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

    protected function createClassTypeFromString(string $class, bool $nullable): ?RestApiBundle\DTO\OpenApi\Schema\ClassType
    {
        $class = ltrim((string) $class, '\\');

        return new RestApiBundle\DTO\OpenApi\Schema\ClassType($class, $nullable);
    }
}
