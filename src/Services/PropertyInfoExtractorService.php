<?php

declare(strict_types=1);

namespace RestApiBundle\Services;

use RestApiBundle;
use Symfony\Component\PropertyInfo;

class PropertyInfoExtractorService
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
        );
    }

    public function getSingleTypeRequired(string $class, string $propertyName): PropertyInfo\Type
    {
        $propertyTypes = $this->propertyInfoExtractor->getTypes($class, $propertyName);
        if (!$propertyTypes) {
            throw new RestApiBundle\Exception\ContextAware\PropertyAwareException('Property has empty type', $class, $propertyName);
        }
        if (\count($propertyTypes) !== 1) {
            throw new RestApiBundle\Exception\ContextAware\PropertyAwareException('Wrong property types count', $class, $propertyName);
        }

        return $propertyTypes[0] ?? throw new \RuntimeException();
    }
}
