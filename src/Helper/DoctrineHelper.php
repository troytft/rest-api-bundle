<?php

declare(strict_types=1);

namespace RestApiBundle\Helper;

use Doctrine;
use RestApiBundle;
use Symfony\Component\PropertyInfo;

class DoctrineHelper
{
    public static function isEntity(string $class): bool
    {
        $reflectionClass = ReflectionHelper::getReflectionClass($class);

        return (bool) AnnotationReader::getClassAnnotation($reflectionClass, Doctrine\ORM\Mapping\Entity::class);
    }

    public static function extractColumnType(string $class, string $field): string
    {
        $reflectionClass = ReflectionHelper::getReflectionClass($class);
        $reflectionProperty = $reflectionClass->getProperty($field);

        $columnAnnotation = AnnotationReader::getPropertyAnnotation($reflectionProperty, Doctrine\ORM\Mapping\Column::class);
        if (!$columnAnnotation instanceof Doctrine\ORM\Mapping\Column) {
            throw new \InvalidArgumentException();
        }

        $type = TypeExtractor::extractByReflectionProperty($reflectionProperty);
        if (!$type) {
            throw new RestApiBundle\Exception\ContextAware\PropertyAwareException('Property has empty type', $reflectionProperty->class, $reflectionProperty->name);
        }

        $allowedTypes = [
            PropertyInfo\Type::BUILTIN_TYPE_INT,
            PropertyInfo\Type::BUILTIN_TYPE_STRING,
        ];

        if (!\in_array($type->getBuiltinType(), $allowedTypes, true)) {
            throw new \InvalidArgumentException();
        }

        return $type->getBuiltinType();
    }
}
