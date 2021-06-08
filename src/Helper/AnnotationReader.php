<?php

namespace RestApiBundle\Helper;

use function array_map;
use function array_merge;

class AnnotationReader
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
        $result = static::getAnnotationReader()->getPropertyAnnotations($reflectionProperty);

        if (\PHP_VERSION_ID >= 80000) {
            $result = array_merge($result, array_map(function (\ReflectionAttribute $reflectionAttribute) {
                $class = $reflectionAttribute->getName();

                return new $class(...$reflectionAttribute->getArguments());
            }, $reflectionProperty->getAttributes()));
        }

        return $result;
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
        $result = static::getAnnotationReader()->getClassAnnotations($reflectionClass);

        if (\PHP_VERSION_ID >= 80000) {
            $result = array_merge($result, array_map(function (\ReflectionAttribute $reflectionAttribute) {
                $class = $reflectionAttribute->getName();

                return new $class(...$reflectionAttribute->getArguments());
            }, $reflectionClass->getAttributes()));
        }

        return $result;
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
        $result = static::getAnnotationReader()->getMethodAnnotations($reflectionMethod);

        if (\PHP_VERSION_ID >= 80000) {
            $result = array_merge($result, array_map(function (\ReflectionAttribute $reflectionAttribute) {
                $class = $reflectionAttribute->getName();

                return new $class(...$reflectionAttribute->getArguments());
            }, $reflectionMethod->getAttributes()));
        }

        return $result;
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
}