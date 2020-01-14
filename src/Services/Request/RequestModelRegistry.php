<?php

namespace RestApiBundle\Services\Request;

use RestApiBundle;
use function array_key_exists;

class RequestModelRegistry
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

    private static function getReflection(string $className): \ReflectionClass
    {
        if (!array_key_exists($className, static::$reflectionCache)) {
            static::$reflectionCache[$className] = new \ReflectionClass($className);
        }

        return static::$reflectionCache[$className];
    }

    public static function isRequestModel(string $className): bool
    {
        if (!array_key_exists($className, static::$reflectionCache)) {
            $reflectionClass = static::getReflection($className);

            static::$classNameCache[$className] = $reflectionClass->isInstantiable()
                && $reflectionClass->implementsInterface(RestApiBundle\RequestModelInterface::class);
        }

        return static::$classNameCache[$className];
    }

    public static function instantiate(string $className): RestApiBundle\RequestModelInterface
    {
        if (!static::isRequestModel($className)) {
            throw new \InvalidArgumentException();
        }

        $model = static::getReflection($className)->newInstance();
        if (!$model instanceof RestApiBundle\RequestModelInterface) {
            throw new \InvalidArgumentException();
        }

        return $model;
    }
}
