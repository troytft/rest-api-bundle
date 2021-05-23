<?php

namespace RestApiBundle\Services\OpenApi\Types;

use RestApiBundle;
use Symfony\Component\PropertyInfo;

use function count;
use function ltrim;

class TypeHintTypeReader extends RestApiBundle\Services\OpenApi\Types\BaseTypeReader
{
    public function resolveReturnType(\ReflectionMethod $reflectionMethod): ?RestApiBundle\Model\OpenApi\Types\TypeInterface
    {
        if (!$reflectionMethod->getReturnType()) {
            return null;
        }

        $type = ltrim((string) $reflectionMethod->getReturnType(), '?');

        return $this->createFromString($type, $reflectionMethod->getReturnType()->allowsNull());
    }

    private function createFromString(string $type, bool $nullable): ?RestApiBundle\Model\OpenApi\Types\TypeInterface
    {
        if ($type === 'array') {
            $result = null;
        } elseif ($type === 'void') {
            $result = new RestApiBundle\Model\OpenApi\Types\NullType();
        } elseif ($this->isScalarType($type)) {
            $result = $this->createScalarTypeFromString($type, $nullable);
        } else {
            $result = $this->createClassTypeFromString($type, $nullable);
        }

        return $result;
    }

    public static function extractByReflectionType(\ReflectionType $sourceReflectionType): ?PropertyInfo\Type
    {
        $result = [];

        if ($sourceReflectionType instanceof \ReflectionUnionType) {
            $reflectionTypes = $sourceReflectionType->getTypes();
        } else {
            $reflectionTypes = [$sourceReflectionType];
        }

        foreach ($reflectionTypes as $reflectionType) {
            $phpTypeOrClass = $sourceReflectionType instanceof \ReflectionNamedType ? $sourceReflectionType->getName() : (string) $reflectionType;
            if ($phpTypeOrClass === PropertyInfo\Type::BUILTIN_TYPE_ARRAY || $phpTypeOrClass === 'mixed') {
                continue;
            }

            if ($phpTypeOrClass === 'null' | $phpTypeOrClass === 'void') {
                $result[] = new PropertyInfo\Type(PropertyInfo\Type::BUILTIN_TYPE_NULL, $sourceReflectionType->allowsNull());
            } elseif ($reflectionType->isBuiltin()) {
                $result[] = new PropertyInfo\Type($phpTypeOrClass, $sourceReflectionType->allowsNull());
            } else {
                $result[] = new PropertyInfo\Type(PropertyInfo\Type::BUILTIN_TYPE_OBJECT, $sourceReflectionType->allowsNull(), $phpTypeOrClass);
            }
        }

        if (count($result) > 1) {
            throw new \InvalidArgumentException('Union types not supported yet');
        }


        return $result[0] ?? null;
    }
}
