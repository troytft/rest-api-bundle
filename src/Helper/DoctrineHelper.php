<?php

namespace RestApiBundle\Helper;

use RestApiBundle;
use Doctrine;
use Symfony\Component\PropertyInfo;

class DoctrineHelper extends RestApiBundle\Services\OpenApi\AbstractSchemaResolver
{
    public static function isEntity(string $class): bool
    {
        $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

        return (bool) RestApiBundle\Helper\AnnotationReader::getClassAnnotation($reflectionClass, Doctrine\ORM\Mapping\Entity::class);
    }

    public static function extractColumnType(string $class, string $field): ?string
    {
        $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);
        $reflectionProperty = $reflectionClass->getProperty($field);

        $columnAnnotation = RestApiBundle\Helper\AnnotationReader::getPropertyAnnotation($reflectionProperty, Doctrine\ORM\Mapping\Column::class);
        if (!$columnAnnotation instanceof Doctrine\ORM\Mapping\Column) {
            return null;
        }

        if ($columnAnnotation->type === 'integer') {
            $result = PropertyInfo\Type::BUILTIN_TYPE_INT;
        } elseif ($columnAnnotation->type === 'string') {
            $result = PropertyInfo\Type::BUILTIN_TYPE_STRING;
        } else {
            $result = null;
        }

        return $result;
    }
}
