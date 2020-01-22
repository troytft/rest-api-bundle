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

        $responseClass = (string) $reflectionMethod->getReturnType();

        return new RestApiBundle\DTO\Docs\ReturnType\ClassType($responseClass, false);
    }
}
