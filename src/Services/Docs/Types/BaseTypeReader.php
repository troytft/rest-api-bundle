<?php

namespace RestApiBundle\Services\Docs\Types;

use RestApiBundle;
use function in_array;
use function ltrim;

abstract class BaseTypeReader
{
    abstract public function resolveReturnType(\ReflectionMethod $reflectionMethod): ?RestApiBundle\DTO\Docs\Types\TypeInterface;

    protected function createScalarTypeFromString(string $type, bool $nullable): ?RestApiBundle\DTO\Docs\Types\ScalarInterface
    {
        switch ($type) {
            case RestApiBundle\Enum\Docs\Types\ScalarType::STRING:
                $result = new RestApiBundle\DTO\Docs\Types\StringType($nullable);

                break;

            case RestApiBundle\Enum\Docs\Types\ScalarType::INT:
            case RestApiBundle\Enum\Docs\Types\ScalarType::INTEGER:
                $result = new RestApiBundle\DTO\Docs\Types\IntegerType($nullable);

                break;

            case RestApiBundle\Enum\Docs\Types\ScalarType::FLOAT:
                $result = new RestApiBundle\DTO\Docs\Types\FloatType($nullable);

                break;

            case RestApiBundle\Enum\Docs\Types\ScalarType::BOOL:
            case RestApiBundle\Enum\Docs\Types\ScalarType::BOOLEAN:
                $result = new RestApiBundle\DTO\Docs\Types\BooleanType($nullable);

                break;

            default:
                throw new \InvalidArgumentException();
        }

        return $result;
    }

    protected function isScalarType(string $type): bool
    {
        return in_array($type, RestApiBundle\Enum\Docs\Types\ScalarType::getValues(), true);
    }

    protected function createClassTypeFromString(string $class, bool $nullable): ?RestApiBundle\DTO\Docs\Types\ClassType
    {
        $class = ltrim((string) $class, '\\');

        return new RestApiBundle\DTO\Docs\Types\ClassType($class, $nullable);
    }
}
