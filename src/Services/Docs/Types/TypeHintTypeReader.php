<?php

namespace RestApiBundle\Services\Docs\Types;

use RestApiBundle;

class TypeHintTypeReader extends RestApiBundle\Services\Docs\Types\BaseTypeReader
{
    public function resolveReturnType(\ReflectionMethod $reflectionMethod): ?RestApiBundle\DTO\Docs\Types\TypeInterface
    {
        if (!$reflectionMethod->getReturnType()) {
            return null;
        }

        return $this->createFromString((string) $reflectionMethod->getReturnType()->getName(), $reflectionMethod->getReturnType()->allowsNull());
    }

    public function resolveParameterType(\ReflectionParameter $reflectionParameter): ?RestApiBundle\DTO\Docs\Types\TypeInterface
    {
        if (!$reflectionParameter->getType()) {
            return null;
        }

        return $this->createFromString($reflectionParameter->getType(), $reflectionParameter->allowsNull());
    }

    private function createFromString(string $type, bool $nullable): ?RestApiBundle\DTO\Docs\Types\TypeInterface
    {
        if ($type === 'array') {
            $result = null;
        } elseif ($type === 'void') {
            $result = new RestApiBundle\DTO\Docs\Types\NullType();
        } elseif ($this->isScalarType($type)) {
            $result = $this->createScalarTypeFromString($type, $nullable);
        } else {
            $result = $this->createClassTypeFromString($type, $nullable);
        }

        return $result;
    }
}
