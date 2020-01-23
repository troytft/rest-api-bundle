<?php

namespace RestApiBundle\Services\Docs;

use RestApiBundle;

class ReflectionHelper
{
    public function getReturnTypeByTypeHint(\ReflectionMethod $reflectionMethod): ?RestApiBundle\DTO\Docs\ReturnType\ReturnTypeInterface
    {
        if (!$reflectionMethod->getReturnType()) {
            return null;
        }

        if ($reflectionMethod->getReturnType()->allowsNull()) {
            throw new \InvalidArgumentException('Not implemented.');
        }

        $class = (string) $reflectionMethod->getReturnType();

        if (!RestApiBundle\Services\Response\ResponseModelHelper::isResponseModel($class)) {
            throw new RestApiBundle\Exception\Docs\ValidationException('Unsupported return type.');
        }

        return new RestApiBundle\DTO\Docs\ReturnType\ClassType($class, false);
    }
}
