<?php

declare(strict_types=1);

namespace RestApiBundle\Services;

use RestApiBundle;
use Symfony\Component\PropertyInfo;

class PropertyInfoExtractorService
{
    private PropertyInfo\Extractor\ReflectionExtractor $reflectionExtractor;

    private PropertyInfo\Extractor\PhpDocExtractor $phpDocExtractor;

    private PropertyInfo\Extractor\PhpStanExtractor $phpStanExtractor;

    private PropertyInfo\PropertyInfoExtractor $propertyInfoExtractor;

    public function __construct()
    {
        $this->reflectionExtractor = new PropertyInfo\Extractor\ReflectionExtractor();
        $this->phpDocExtractor = new PropertyInfo\Extractor\PhpDocExtractor();
        $this->phpStanExtractor = new PropertyInfo\Extractor\PhpStanExtractor();
        $this->propertyInfoExtractor = new PropertyInfo\PropertyInfoExtractor(
            [$this->reflectionExtractor],
            [$this->phpDocExtractor, $this->reflectionExtractor],
            [$this->reflectionExtractor],
            [$this->reflectionExtractor, $this->phpDocExtractor],
            [$this->reflectionExtractor],
        );
    }

    public function getRequiredPropertyType(string $class, string $property): PropertyInfo\Type
    {
        $types = $this->propertyInfoExtractor->getTypes($class, $property);
        if (!$types) {
            throw new RestApiBundle\Exception\ContextAware\PropertyAwareException('Property has empty type', $class, $property);
        }

        if (\count($types) !== 1) {
            throw new RestApiBundle\Exception\ContextAware\PropertyAwareException('Wrong property types count', $class, $property);
        }

        return $types[0] ?? throw new \RuntimeException();
    }
}
