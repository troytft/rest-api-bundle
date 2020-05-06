<?php

namespace RestApiBundle\Services\Docs\Schema;

use RestApiBundle;

class TypeHintReader extends RestApiBundle\Services\Docs\Schema\BaseReader
{
    public function getMethodReturnSchema(\ReflectionMethod $reflectionMethod): ?RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
    {
        if (!$reflectionMethod->getReturnType()) {
            return null;
        }


        return $this->createFromString((string) $reflectionMethod->getReturnType(), $reflectionMethod->getReturnType()->allowsNull());
    }

    public function getMethodParameterSchema(\ReflectionParameter $reflectionParameter): ?RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
    {
        if (!$reflectionParameter->getType()) {
            return null;
        }

        return $this->createFromString($reflectionParameter->getType(), $reflectionParameter->allowsNull());
    }

    private function createFromString(string $type, bool $nullable): ?RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
    {
        if ($type === 'array') {
            $result = null;
        } elseif ($type === 'void') {
            $result = new RestApiBundle\DTO\Docs\Schema\NullType();
        } elseif ($this->isScalarType($type)) {
            $result = $this->createScalarTypeFromString($type, $nullable);
        } else {
            $result = $this->createClassTypeFromString($type, $nullable);
        }

        return $result;
    }
}
