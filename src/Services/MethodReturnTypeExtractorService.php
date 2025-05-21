<?php

declare(strict_types=1);

namespace RestApiBundle\Services;

use RestApiBundle;
use Symfony\Component\PropertyInfo;

class MethodReturnTypeExtractorService
{
    private PropertyInfo\Extractor\ReflectionExtractor $reflectionExtractor;

    private PropertyInfo\Extractor\PhpDocExtractor $phpDocExtractor;

    private PropertyInfo\PropertyInfoExtractor $propertyInfoExtractor;

    public function __construct()
    {
        $this->reflectionExtractor = new PropertyInfo\Extractor\ReflectionExtractor();
        $this->phpDocExtractor = new PropertyInfo\Extractor\PhpDocExtractor();
        $this->propertyInfoExtractor = new PropertyInfo\PropertyInfoExtractor(
            [$this->reflectionExtractor],
            [$this->phpDocExtractor, $this->reflectionExtractor],
            [$this->phpDocExtractor],
            [$this->reflectionExtractor, $this->phpDocExtractor],
            [$this->reflectionExtractor],
        );
    }

    public function getTypeOptional(\ReflectionMethod $reflectionMethod): ?PropertyInfo\Type
    {
        if (!\str_starts_with($reflectionMethod->getName(), 'get')) {
            throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Method name must start with "get"', $reflectionMethod);
        }
        $propertyName = \lcfirst(\substr($reflectionMethod->name, 3));

        $types = $this->propertyInfoExtractor->getTypes($reflectionMethod->class, $propertyName);
        if (!$types) {
            return null;
        }

        if (\count($types) !== 1) {
            throw new RestApiBundle\Exception\ContextAware\ReflectionMethodAwareException('Wrong method return types count', $reflectionMethod);
        }

        return $types[0] ?? throw new \RuntimeException();
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
