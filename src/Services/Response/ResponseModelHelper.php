<?php

namespace RestApiBundle\Services\Response;

use RestApiBundle;
use function array_key_exists;

class ResponseModelHelper
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

    public static function isResponseModel(string $className): bool
    {
        if (!array_key_exists($className, static::$reflectionCache)) {
            $reflectionClass = static::getReflection($className);

            static::$classNameCache[$className] = $reflectionClass->isInstantiable()
                && $reflectionClass->implementsInterface(RestApiBundle\ResponseModelInterface::class);
        }

        return static::$classNameCache[$className];
    }
}
