<?php

namespace RestApiBundle\Helper;

use RestApiBundle;
use Doctrine;
use Symfony\Component\PropertyInfo;

use function sprintf;

class DoctrineHelper extends RestApiBundle\Services\OpenApi\AbstractSchemaResolver
{
    private static ?Doctrine\Common\Annotations\AnnotationReader $annotationReader = null;

    public static function isEntity(string $class): bool
    {
        $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

        return (bool) static::getAnnotationReader()->getClassAnnotation($reflectionClass, Doctrine\ORM\Mapping\Entity::class);
    }

    public static function extractColumnType(string $class, string $field): string
    {
        $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);
        $reflectionProperty = $reflectionClass->getProperty($field);

        $columnAnnotation = static::getAnnotationReader()->getPropertyAnnotation($reflectionProperty, Doctrine\ORM\Mapping\Column::class);
        if (!$columnAnnotation instanceof Doctrine\ORM\Mapping\Column) {
            throw new \InvalidArgumentException();
        }

        switch ($columnAnnotation->type) {
            case 'integer':
                $result = PropertyInfo\Type::BUILTIN_TYPE_INT;

                break;

            case 'string':
                $result = PropertyInfo\Type::BUILTIN_TYPE_STRING;

                break;

            default:
                throw new \InvalidArgumentException(sprintf('Unknown column type %s', $columnAnnotation->type));
        }

        return $result;
    }

    private static function getAnnotationReader(): Doctrine\Common\Annotations\AnnotationReader
    {
        if (!static::$annotationReader) {
            static::$annotationReader = RestApiBundle\Helper\AnnotationReaderFactory::create(true);
        }

        return static::$annotationReader;
    }
}
