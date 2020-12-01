<?php

namespace RestApiBundle\Services\OpenApi\Reader;

use RestApiBundle;

class TypeHintReader extends RestApiBundle\Services\OpenApi\Reader\BaseReader
{
    public function getMethodReturnSchema(\ReflectionMethod $reflectionMethod): ?RestApiBundle\DTO\OpenApi\Schema\TypeInterface
    {
        if (!$reflectionMethod->getReturnType()) {
            return null;
        }


        return $this->createFromString((string) $reflectionMethod->getReturnType(), $reflectionMethod->getReturnType()->allowsNull());
    }

    public function getTypeByReflectionParameter(\ReflectionParameter $reflectionParameter): ?RestApiBundle\DTO\OpenApi\Schema\TypeInterface
    {
        if (!$reflectionParameter->getType()) {
            return null;
        }

        return $this->createFromString($reflectionParameter->getType(), $reflectionParameter->allowsNull());
    }

    private function createFromString(string $type, bool $nullable): ?RestApiBundle\DTO\OpenApi\Schema\TypeInterface
    {
        if ($type === 'array') {
            $result = null;
        } elseif ($type === 'void') {
            $result = new RestApiBundle\DTO\OpenApi\Schema\NullType();
        } elseif ($this->isScalarType($type)) {
            $result = $this->createScalarTypeFromString($type, $nullable);
        } else {
            $result = $this->createClassTypeFromString($type, $nullable);
        }

        return $result;
    }
}
