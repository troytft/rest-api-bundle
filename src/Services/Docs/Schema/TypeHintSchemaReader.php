<?php

namespace RestApiBundle\Services\Docs\Schema;

use RestApiBundle;

class TypeHintSchemaReader extends RestApiBundle\Services\Docs\Schema\BaseSchemaReader
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

    protected function createFromString(string $type, bool $nullable): ?RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
    {
        if ($type === 'array') {
            return null;
        }

        return $this->createFromString($type, $nullable);
    }
}
