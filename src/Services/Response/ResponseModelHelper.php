<?php

namespace RestApiBundle\Services\Response;

use RestApiBundle;

use function array_key_exists;
use function class_exists;

class ResponseModelHelper
{
    /**
     * @var array<string,bool>
     */
    private static $classNameCache = [];

    public static function isResponseModel(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        if (!array_key_exists($class, static::$classNameCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$classNameCache[$class] = $reflectionClass->isInstantiable()
                && $reflectionClass->implementsInterface(RestApiBundle\Mapping\ResponseModel\ResponseModelInterface::class);
        }

        return static::$classNameCache[$class];
    }
}
