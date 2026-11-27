<?php

declare(strict_types=1);

namespace RestApiBundle\Helper;

final class AttributeReader
{
    /**
     * @return list<object>
     */
    public static function getPropertyAttributes(\ReflectionProperty $reflectionProperty): array
    {
        return static::createInstances($reflectionProperty->getAttributes());
    }

    /**
     * @param \ReflectionClass<object> $reflectionClass
     *
     * @return list<object>
     */
    public static function getClassAttributes(\ReflectionClass $reflectionClass): array
    {
        return static::createInstances($reflectionClass->getAttributes());
    }

    /**
     * @template T of object
     *
     * @param \ReflectionClass<object> $reflectionClass
     * @param class-string<T> $class
     *
     * @return T|null
     */
    public static function getClassAttribute(\ReflectionClass $reflectionClass, string $class): ?object
    {
        foreach (static::getClassAttributes($reflectionClass) as $classAttribute) {
            if ($classAttribute instanceof $class) {
                return $classAttribute;
            }
        }

        return null;
    }

    /**
     * @return list<object>
     */
    public static function getMethodAttributes(\ReflectionMethod $reflectionMethod): array
    {
        return static::createInstances($reflectionMethod->getAttributes());
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return T|null
     */
    public static function getMethodAttribute(\ReflectionMethod $reflectionMethod, string $class): ?object
    {
        foreach (static::getMethodAttributes($reflectionMethod) as $methodAttribute) {
            if ($methodAttribute instanceof $class) {
                return $methodAttribute;
            }
        }

        return null;
    }

    /**
     * @param list<\ReflectionAttribute<object>> $attributes
     *
     * @return list<object>
     */
    private static function createInstances(array $attributes): array
    {
        return \array_map(function (\ReflectionAttribute $reflectionAttribute) {
            $class = $reflectionAttribute->getName();

            return new $class(...$reflectionAttribute->getArguments());
        }, $attributes);
    }
}
