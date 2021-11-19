<?php

namespace RestApiBundle\Helper;

use function array_map;
use function array_merge;

final class AnnotationReader
{
    private static ?\Doctrine\Common\Annotations\AnnotationReader $annotationReader = null;

    private static function getAnnotationReader(): \Doctrine\Common\Annotations\AnnotationReader
    {
        if (!isset(static::$annotationReader)) {
            $parser = new \Doctrine\Common\Annotations\DocParser();
            $parser
                ->setIgnoreNotImportedAnnotations(true);

            static::$annotationReader = new \Doctrine\Common\Annotations\AnnotationReader($parser);
        }

        return static::$annotationReader;
    }

    public static function getPropertyAnnotations(\ReflectionProperty $reflectionProperty): array
    {
        return array_merge(
            static::getAnnotationReader()->getPropertyAnnotations($reflectionProperty),
            static::createAnnotationsFromAttributes($reflectionProperty->getAttributes())
        );
    }

    public static function getPropertyAnnotation(\ReflectionProperty $reflectionProperty, string $class)
    {
        foreach (static::getPropertyAnnotations($reflectionProperty) as $propertyAnnotation) {
            if ($propertyAnnotation instanceof $class) {
                return $propertyAnnotation;
            }
        }

        return null;
    }

    public static function getClassAnnotations(\ReflectionClass $reflectionClass): array
    {
        return array_merge(
            static::getAnnotationReader()->getClassAnnotations($reflectionClass),
            static::createAnnotationsFromAttributes($reflectionClass->getAttributes())
        );
    }

    public static function getClassAnnotation(\ReflectionClass $reflectionClass, string $class)
    {
        foreach (static::getClassAnnotations($reflectionClass) as $classAnnotation) {
            if ($classAnnotation instanceof $class) {
                return $classAnnotation;
            }
        }

        return null;
    }

    public static function getMethodAnnotations(\ReflectionMethod $reflectionMethod): array
    {
        return array_merge(
            static::getAnnotationReader()->getMethodAnnotations($reflectionMethod),
            static::createAnnotationsFromAttributes($reflectionMethod->getAttributes())
        );
    }


    public static function getMethodAnnotation(\ReflectionMethod $reflectionMethod, string $class)
    {
        foreach (static::getMethodAnnotations($reflectionMethod) as $methodAnnotation) {
            if ($methodAnnotation instanceof $class) {
                return $methodAnnotation;
            }
        }

        return null;
    }

    private static function createAnnotationsFromAttributes(array $attributes): array
    {
        return array_map(function (\ReflectionAttribute $reflectionAttribute) {
            $class = $reflectionAttribute->getName();
            return new $class(...$reflectionAttribute->getArguments());
        }, $attributes);
    }
}
