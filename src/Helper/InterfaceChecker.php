<?php

namespace RestApiBundle\Helper;

use RestApiBundle;

use function class_exists;

final class InterfaceChecker
{
    public static function isResponseModel(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

        return $reflectionClass->isInstantiable() && $reflectionClass->implementsInterface(RestApiBundle\Mapping\ResponseModel\ResponseModelInterface::class);
    }

    public static function isDateTime(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

        return $reflectionClass->isInstantiable() && $reflectionClass->implementsInterface(\DateTimeInterface::class);
    }

    public static function isMapperDate(string $class): bool
    {
        $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

        return $reflectionClass->isInstantiable() && $reflectionClass->implementsInterface(RestApiBundle\Mapping\Mapper\DateInterface::class);
    }

    public static function isResponseModelEnum(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

        return $reflectionClass->implementsInterface(RestApiBundle\Mapping\ResponseModel\EnumInterface::class);
    }

    public static function isMapperEnum(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

        return $reflectionClass->implementsInterface(RestApiBundle\Mapping\Mapper\EnumInterface::class);
    }

    public static function isResponseModelDate(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

        return $reflectionClass->implementsInterface(RestApiBundle\Mapping\ResponseModel\DateInterface::class);
    }

    public static function isMapperModel(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

        return $reflectionClass->isInstantiable() && $reflectionClass->implementsInterface(RestApiBundle\Mapping\Mapper\ModelInterface::class);
    }

    public static function isRequestModel(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

        return $reflectionClass->isInstantiable() && $reflectionClass->implementsInterface(RestApiBundle\Mapping\RequestModel\RequestModelInterface::class);
    }
}
