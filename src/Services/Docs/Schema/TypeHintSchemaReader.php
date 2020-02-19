<?php

namespace RestApiBundle\Services\Docs\Schema;

use RestApiBundle;

class TypeHintSchemaReader
{
    public function getReturnType(\ReflectionMethod $reflectionMethod): ?RestApiBundle\DTO\Docs\Schema\TypeInterface
    {
        $returnType = $reflectionMethod->getReturnType();
        if (!$returnType) {
            return null;
        }


        return new RestApiBundle\DTO\Docs\Schema\ClassType((string) $returnType, $returnType->allowsNull());
    }

    public function getParameterTypeByReflectionParameter(\ReflectionParameter $reflectionParameter): ?RestApiBundle\DTO\Docs\Schema\TypeInterface
    {
        if (!$reflectionParameter->getType()) {
            return null;
        }

        return $this->getTypeFromReflectionType($reflectionParameter->getType());
    }

    private function getTypeFromReflectionType(\ReflectionType $reflectionType): RestApiBundle\DTO\Docs\Schema\TypeInterface
    {
        $type = (string) $reflectionType;

        switch ($type) {
            case 'string':
                $result = new RestApiBundle\DTO\Docs\Schema\StringType($reflectionType->allowsNull());

                break;

            case 'int':
            case 'integer':
                $result = new RestApiBundle\DTO\Docs\Schema\IntegerType($reflectionType->allowsNull());

                break;

            case 'float':
                $result = new RestApiBundle\DTO\Docs\Schema\FloatType($reflectionType->allowsNull());

                break;

            case 'bool':
            case 'boolean':
                $result = new RestApiBundle\DTO\Docs\Schema\BooleanType($reflectionType->allowsNull());

                break;

            default:
                $result = new RestApiBundle\DTO\Docs\Schema\ClassType($type, $reflectionType->allowsNull());
        }

        return $result;
    }
}
