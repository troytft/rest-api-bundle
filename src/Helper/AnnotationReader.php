<?php

declare(strict_types=1);

namespace RestApiBundle\Helper;

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

    /**
     * @return list<object>
     */
    public static function getPropertyAnnotations(\ReflectionProperty $reflectionProperty): array
    {
        return \array_values(\array_merge(
            static::getAnnotationReader()->getPropertyAnnotations($reflectionProperty),
            static::createAnnotationsFromAttributes($reflectionProperty->getAttributes())
        ));
    }

    /**
     * @param \ReflectionClass<object> $reflectionClass
     *
     * @return list<object>
     */
    public static function getClassAnnotations(\ReflectionClass $reflectionClass): array
    {
        return \array_values(\array_merge(
            static::getAnnotationReader()->getClassAnnotations($reflectionClass),
            static::createAnnotationsFromAttributes($reflectionClass->getAttributes())
        ));
    }

    /**
     * @template T of object
     *
     * @param \ReflectionClass<object> $reflectionClass
     * @param class-string<T> $class
     *
     * @return T|null
     */
    public static function getClassAnnotation(\ReflectionClass $reflectionClass, string $class): ?object
    {
        foreach (static::getClassAnnotations($reflectionClass) as $classAnnotation) {
            if ($classAnnotation instanceof $class) {
                return $classAnnotation;
            }
        }

        return null;
    }

    /**
     * @return list<object>
     */
    public static function getMethodAnnotations(\ReflectionMethod $reflectionMethod): array
    {
        return \array_values(\array_merge(
            static::getAnnotationReader()->getMethodAnnotations($reflectionMethod),
            static::createAnnotationsFromAttributes($reflectionMethod->getAttributes())
        ));
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return T|null
     */
    public static function getMethodAnnotation(\ReflectionMethod $reflectionMethod, string $class): ?object
    {
        foreach (static::getMethodAnnotations($reflectionMethod) as $methodAnnotation) {
            if ($methodAnnotation instanceof $class) {
                return $methodAnnotation;
            }
        }

        return null;
    }

    /**
     * @param list<\ReflectionAttribute<object>> $attributes
     *
     * @return list<object>
     */
    private static function createAnnotationsFromAttributes(array $attributes): array
    {
        return \array_map(function (\ReflectionAttribute $reflectionAttribute) {
            $class = $reflectionAttribute->getName();

            return new $class(...$reflectionAttribute->getArguments());
        }, $attributes);
    }
}
