<?php

namespace RestApiBundle\Services\Request;

use RestApiBundle;
use function array_key_exists;

class RequestModelHelper
{
    /**
     * @var array<string,bool>
     */
    private static $classNameCache = [];

    private function __construct()
    {
    }

    public static function isRequestModel(string $className): bool
    {
        if (!array_key_exists($className, static::$classNameCache)) {
            $reflectionClass = RestApiBundle\Services\ReflectionClassStore::get($className);

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

        $model = RestApiBundle\Services\ReflectionClassStore::get($className)->newInstance();
        if (!$model instanceof RestApiBundle\RequestModelInterface) {
            throw new \InvalidArgumentException();
        }

        return $model;
    }
}
