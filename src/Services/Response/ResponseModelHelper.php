<?php

namespace RestApiBundle\Services\Response;

use RestApiBundle;
use function array_key_exists;

class ResponseModelHelper
{
    /**
     * @var array<string,bool>
     */
    private static $classNameCache = [];

    public static function isResponseModel(string $className): bool
    {
        if (!array_key_exists($className, static::$classNameCache)) {
            $reflectionClass = RestApiBundle\Services\ReflectionClassStore::get($className);

            static::$classNameCache[$className] = $reflectionClass->isInstantiable()
                && $reflectionClass->implementsInterface(RestApiBundle\ResponseModelInterface::class);
        }

        return static::$classNameCache[$className];
    }
}
