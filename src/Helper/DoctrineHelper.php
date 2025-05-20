<?php

declare(strict_types=1);

namespace RestApiBundle\Helper;

use RestApiBundle;
use Doctrine;
use Symfony\Component\PropertyInfo;

use function in_array;

class DoctrineHelper
{
    public static function isEntity(string $class): bool
    {
        $reflectionClass = RestApiBundle\Helper\ReflectionHelper::getReflectionClass($class);

        return (bool) RestApiBundle\Helper\AnnotationReader::getClassAnnotation($reflectionClass, Doctrine\ORM\Mapping\Entity::class);
    }

    public static function extractColumnType(string $class, string $field): string
    {
        $reflectionClass = RestApiBundle\Helper\ReflectionHelper::getReflectionClass($class);
        $reflectionProperty = $reflectionClass->getProperty($field);


        $columnAnnotation = RestApiBundle\Helper\AnnotationReader::getPropertyAnnotation($reflectionProperty, Doctrine\ORM\Mapping\Column::class);
        if (!$columnAnnotation instanceof Doctrine\ORM\Mapping\Column) {
            throw new \InvalidArgumentException();
        }

        $type = RestApiBundle\Helper\TypeExtractor::extractByReflectionProperty($reflectionProperty);
        if (!$type) {
            throw new RestApiBundle\Exception\ContextAware\ReflectionPropertyAwareException('Property has empty type', $reflectionProperty);
        }

        $allowedTypes = [
            PropertyInfo\Type::BUILTIN_TYPE_INT,
            PropertyInfo\Type::BUILTIN_TYPE_STRING,
        ];

        if (!in_array($type->getBuiltinType(), $allowedTypes, true)) {
            throw new \InvalidArgumentException();
        }

        return $type->getBuiltinType();
    }
}
