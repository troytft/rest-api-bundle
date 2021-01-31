<?php

namespace RestApiBundle\Services\OpenApi\Schema;

use RestApiBundle;

class TypeHintReader extends RestApiBundle\Services\OpenApi\Schema\BaseReader
{
    public function getMethodReturnSchema(\ReflectionMethod $reflectionMethod): ?RestApiBundle\DTO\OpenApi\Types\TypeInterface
    {
        if (!$reflectionMethod->getReturnType()) {
            return null;
        }


        return $this->createFromString((string) $reflectionMethod->getReturnType(), $reflectionMethod->getReturnType()->allowsNull());
    }

    public function getMethodParameterSchema(\ReflectionParameter $reflectionParameter): ?RestApiBundle\DTO\OpenApi\Types\TypeInterface
    {
        if (!$reflectionParameter->getType()) {
            return null;
        }

        return $this->createFromString($reflectionParameter->getType(), $reflectionParameter->allowsNull());
    }

    private function createFromString(string $type, bool $nullable): ?RestApiBundle\DTO\OpenApi\Types\TypeInterface
    {
        if ($type === 'array') {
            $result = null;
        } elseif ($type === 'void') {
            $result = new RestApiBundle\DTO\OpenApi\Types\NullType();
        } elseif ($this->isScalarType($type)) {
            $result = $this->createScalarTypeFromString($type, $nullable);
        } else {
            $result = $this->createClassTypeFromString($type, $nullable);
        }

        return $result;
    }
}
