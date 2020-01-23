<?php

namespace RestApiBundle\Services\Docs;

use RestApiBundle;

class TypeHintHelper
{
    public function getReturnTypeByReflectionMethod(\ReflectionMethod $reflectionMethod): ?RestApiBundle\DTO\Docs\ReturnType\ReturnTypeInterface
    {
        if (!$reflectionMethod->getReturnType()) {
            return null;
        }

        $class = (string) $reflectionMethod->getReturnType();

        if (!RestApiBundle\Services\Response\ResponseModelHelper::isResponseModel($class)) {
            throw new RestApiBundle\Exception\Docs\InvalidDefinition\UnsupportedReturnTypeException();
        }

        return new RestApiBundle\DTO\Docs\ReturnType\ObjectType($class, $reflectionMethod->getReturnType()->allowsNull());
    }
}
