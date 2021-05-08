<?php

namespace RestApiBundle\Services\OpenApi\Types;

use RestApiBundle;

use function in_array;
use function ltrim;

abstract class BaseTypeReader
{
    abstract public function resolveReturnType(\ReflectionMethod $reflectionMethod): ?RestApiBundle\Model\OpenApi\Types\TypeInterface;

    protected function createScalarTypeFromString(string $type, bool $nullable): ?RestApiBundle\Model\OpenApi\Types\ScalarInterface
    {
        switch ($type) {
            case RestApiBundle\Enum\OpenApi\Types\ScalarType::STRING:
                $result = new RestApiBundle\Model\OpenApi\Types\StringType($nullable);

                break;

            case RestApiBundle\Enum\OpenApi\Types\ScalarType::INT:
            case RestApiBundle\Enum\OpenApi\Types\ScalarType::INTEGER:
                $result = new RestApiBundle\Model\OpenApi\Types\IntegerType($nullable);

                break;

            case RestApiBundle\Enum\OpenApi\Types\ScalarType::FLOAT:
                $result = new RestApiBundle\Model\OpenApi\Types\FloatType($nullable);

                break;

            case RestApiBundle\Enum\OpenApi\Types\ScalarType::BOOL:
            case RestApiBundle\Enum\OpenApi\Types\ScalarType::BOOLEAN:
                $result = new RestApiBundle\Model\OpenApi\Types\BooleanType($nullable);

                break;

            default:
                throw new \InvalidArgumentException();
        }

        return $result;
    }

    protected function isScalarType(string $type): bool
    {
        return in_array($type, RestApiBundle\Enum\OpenApi\Types\ScalarType::getValues(), true);
    }

    protected function createClassTypeFromString(string $class, bool $nullable): ?RestApiBundle\Model\OpenApi\Types\ClassType
    {
        $class = ltrim((string) $class, '\\');

        return new RestApiBundle\Model\OpenApi\Types\ClassType($class, $nullable);
    }
}
