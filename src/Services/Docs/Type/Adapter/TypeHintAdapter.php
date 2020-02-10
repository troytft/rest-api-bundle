<?php

namespace RestApiBundle\Services\Docs\Type\Adapter;

use RestApiBundle;

class TypeHintAdapter
{
    public function getReturnType(\ReflectionMethod $reflectionMethod): ?RestApiBundle\DTO\Docs\Type\TypeInterface
    {
        $returnType = $reflectionMethod->getReturnType();
        if (!$returnType) {
            return null;
        }


        return new RestApiBundle\DTO\Docs\Type\ClassType((string) $returnType, $returnType->allowsNull());
    }

    public function getParameterTypeByReflectionParameter(\ReflectionParameter $reflectionParameter): ?RestApiBundle\DTO\Docs\Type\TypeInterface
    {
        if (!$reflectionParameter->getType()) {
            return null;
        }

        return $this->getTypeFromReflectionType($reflectionParameter->getType());
    }

    private function getTypeFromReflectionType(\ReflectionType $reflectionType): RestApiBundle\DTO\Docs\Type\TypeInterface
    {
        $type = (string) $reflectionType;

        switch ($type) {
            case 'string':
                $result = new RestApiBundle\DTO\Docs\Type\StringType($reflectionType->allowsNull());

                break;

            case 'int':
            case 'integer':
                $result = new RestApiBundle\DTO\Docs\Type\IntegerType($reflectionType->allowsNull());

                break;

            case 'float':
                $result = new RestApiBundle\DTO\Docs\Type\FloatType($reflectionType->allowsNull());

                break;

            case 'bool':
            case 'boolean':
                $result = new RestApiBundle\DTO\Docs\Type\BooleanType($reflectionType->allowsNull());

                break;

            default:
                $result = new RestApiBundle\DTO\Docs\Type\ClassType($type, $reflectionType->allowsNull());
        }

        return $result;
    }
}
