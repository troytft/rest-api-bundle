<?php

namespace RestApiBundle\Services\Docs\Schema;

use RestApiBundle;

class TypeHintSchemaReader extends RestApiBundle\Services\Docs\Schema\BaseSchemaReader
{
    public function getMethodReturnSchema(\ReflectionMethod $reflectionMethod): ?RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
    {
        $returnType = $reflectionMethod->getReturnType();
        if (!$returnType) {
            return null;
        }


        return new RestApiBundle\DTO\Docs\Schema\ClassType((string) $returnType, $returnType->allowsNull());
    }

    public function getMethodParameterSchema(\ReflectionParameter $reflectionParameter): ?RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
    {
        if (!$reflectionParameter->getType()) {
            return null;
        }

        return $this->createFromString($reflectionParameter->getType(), $reflectionParameter->allowsNull());
    }
}
