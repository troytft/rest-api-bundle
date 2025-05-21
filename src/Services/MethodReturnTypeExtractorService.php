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
