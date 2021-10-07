<?php

namespace RestApiBundle\Helper;

use RestApiBundle;
use Doctrine;
use Symfony\Component\PropertyInfo;

use function in_array;

class DoctrineHelper extends RestApiBundle\Services\OpenApi\AbstractSchemaResolver
{
    public static function isEntity(string $class): bool
    {
        $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

        return (bool) RestApiBundle\Helper\AnnotationReader::getClassAnnotation($reflectionClass, Doctrine\ORM\Mapping\Entity::class);
    }

    public static function extractColumnType(string $class, string $field): string
    {
        $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);
        $reflectionProperty = $reflectionClass->getProperty($field);


        $columnAnnotation = RestApiBundle\Helper\AnnotationReader::getPropertyAnnotation($reflectionProperty, Doctrine\ORM\Mapping\Column::class);
        if (!$columnAnnotation instanceof Doctrine\ORM\Mapping\Column) {
            throw new \InvalidArgumentException();
        }

        $type = RestApiBundle\Helper\TypeExtractor::extractPropertyType($reflectionProperty);
        if (!$type) {
            throw new RestApiBundle\Exception\ContextAware\PropertyOfClassException('Property has empty type', $reflectionProperty->getDeclaringClass()->getName(), $reflectionProperty->getName());
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
