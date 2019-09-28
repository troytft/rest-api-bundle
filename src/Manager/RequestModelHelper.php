<?php

namespace RestApiBundle\Manager;

use RestApiBundle\RequestModelInterface;
use function array_key_exists;

class RequestModelHelper
{
    /**
     * @var array<string,\ReflectionClass>
     */
    private static $reflectionCache = [];

    /**
     * @var array<string,bool>
     */
    private static $classNameCache = [];

    private function __construct()
    {
    }

    /**
     * @param string $className
     *
     * @return \ReflectionClass
     */
    private static function getReflection(string $className): \ReflectionClass
    {
        if (!array_key_exists($className, static::$reflectionCache)) {
            static::$reflectionCache[$className] = new \ReflectionClass($className);
        }

        return static::$reflectionCache[$className];
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    public static function isRequestModel(string $className): bool
    {
        if (!array_key_exists($className, static::$reflectionCache)) {
            $reflectionClass = static::getReflection($className);

            static::$classNameCache[$className] = $reflectionClass->isInstantiable()
                && $reflectionClass->implementsInterface(RequestModelInterface::class);
        }

        return static::$classNameCache[$className];
    }

    /**
     * @param string $className
     *
     * @return RequestModelInterface
     */
    public static function instantiate(string $className)
    {
        if (!static::isRequestModel($className)) {
            throw new \InvalidArgumentException();
        }

        return static::getReflection($className)->newInstance();
    }
}
