<?php

declare(strict_types=1);

namespace RestApiBundle\Services;

use RestApiBundle;
use Symfony\Component\PropertyInfo;

class MethodReturnTypeExtractorService
{
    public function getTypeOptional(\ReflectionMethod $reflectionMethod): ?PropertyInfo\Type
    {
        return RestApiBundle\Helper\TypeExtractor::extractByReflectionMethod($reflectionMethod);
    }

    public function getTypeRequired(\ReflectionMethod $reflectionMethod): PropertyInfo\Type
    {
        $type = $this->getTypeOptional($reflectionMethod);
        if (!$type) {
            throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Empty property type', $reflectionMethod);
        }

        return $type;
    }
}
