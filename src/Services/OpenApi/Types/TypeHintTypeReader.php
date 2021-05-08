<?php

namespace RestApiBundle\Services\OpenApi\Types;

use RestApiBundle;

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

    public function resolveParameterType(\ReflectionParameter $reflectionParameter): ?RestApiBundle\Model\OpenApi\Types\TypeInterface
    {
        if (!$reflectionParameter->getType()) {
            return null;
        }

        return $this->createFromString($reflectionParameter->getType(), $reflectionParameter->allowsNull());
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
}
