<?php

declare(strict_types=1);

namespace RestApiBundle\Services;

use RestApiBundle;
use Symfony\Component\PropertyInfo;

class PropertyTypeExtractorService
{
    private PropertyInfo\Extractor\ReflectionExtractor $reflectionExtractor;

    private PropertyInfo\Extractor\PhpDocExtractor $phpDocExtractor;

    private PropertyInfo\PropertyInfoExtractor $propertyInfoExtractor;

    public function __construct()
    {
        $this->reflectionExtractor = new PropertyInfo\Extractor\ReflectionExtractor([], []); // disable getters/setters
        $this->phpDocExtractor = new PropertyInfo\Extractor\PhpDocExtractor();
        $this->propertyInfoExtractor = new PropertyInfo\PropertyInfoExtractor(
            [$this->reflectionExtractor],
            [$this->phpDocExtractor, $this->reflectionExtractor],
            [$this->phpDocExtractor],
            [$this->reflectionExtractor, $this->phpDocExtractor],
            [$this->reflectionExtractor],
        );
    }

    public function getTypeOptional(string $class, string $property): ?PropertyInfo\Type
    {
        if (!\property_exists($class, $property) && !\method_exists($class, $property)) {
            throw new RestApiBundle\Exception\ContextAware\PropertyAwareException('Property not exist in class', $class, $property);
        }

        $types = $this->propertyInfoExtractor->getTypes($class, $property);
        if (!$types) {
            return null;
        }

        if (\count($types) !== 1) {
            throw new RestApiBundle\Exception\ContextAware\PropertyAwareException('Wrong property types count', $class, $property);
        }

        return $types[0] ?? throw new \RuntimeException();
    }

    public function getTypeRequired(string $class, string $property): PropertyInfo\Type
    {
        $type = $this->getTypeOptional($class, $property);
        if (!$type) {
            throw new RestApiBundle\Exception\ContextAware\PropertyAwareException('Empty property type', $class, $property);
        }

        return $type;
    }
}
