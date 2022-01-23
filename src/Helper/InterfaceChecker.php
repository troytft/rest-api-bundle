<?php

namespace RestApiBundle\Helper;

use RestApiBundle;

use function array_key_exists;
use function class_exists;

final class InterfaceChecker
{
    /**
     * @var array<string, bool>
     */
    private static array $responseModelInterfaceCache = [];

    /**
     * @var array<string, bool>
     */
    private static array $dateTimeInterfaceCache = [
        \DateTime::class => true,
    ];

    /**
     * @var array<string, bool>
     */
    private static array $mapperDateInterfaceCache = [
        RestApiBundle\Mapping\Mapper\Date::class => true,
    ];

    /**
     * @var array<string, bool>
     */
    private static array $responseModelEnumInterfaceCache = [];

    /**
     * @var array<string, bool>
     */
    private static array $mapperEnumInterfaceCache = [];

    /**
     * @var array<string, bool>
     */
    private static array $responseModelDateInterfaceCache = [];

    /**
     * @var array<string, bool>
     */
    private static array $mapperModelInterfaceCache = [];

    /**
     * @var array<string, bool>
     */
    private static array $requestModelInterfaceCache = [];

    public static function isResponseModelInterface(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        if (!array_key_exists($class, static::$responseModelInterfaceCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$responseModelInterfaceCache[$class] = $reflectionClass->isInstantiable()
                && $reflectionClass->implementsInterface(RestApiBundle\Mapping\ResponseModel\ResponseModelInterface::class);
        }

        return static::$responseModelInterfaceCache[$class];
    }

    public static function isDateTimeInterface(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        if (!array_key_exists($class, static::$dateTimeInterfaceCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$dateTimeInterfaceCache[$class] = $reflectionClass->isInstantiable()
                && $reflectionClass->implementsInterface(\DateTimeInterface::class);
        }

        return static::$dateTimeInterfaceCache[$class];
    }

    public static function isMapperDateInterface(string $class): bool
    {
        if (!array_key_exists($class, static::$mapperDateInterfaceCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$mapperDateInterfaceCache[$class] = $reflectionClass->isInstantiable() && $reflectionClass->implementsInterface(RestApiBundle\Mapping\Mapper\DateInterface::class);
        }

        return static::$mapperDateInterfaceCache[$class];
    }

    public static function isResponseModelEnumInterface(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        if (!array_key_exists($class, static::$responseModelEnumInterfaceCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$responseModelEnumInterfaceCache[$class] = $reflectionClass->implementsInterface(RestApiBundle\Mapping\ResponseModel\EnumInterface::class);
        }

        return static::$responseModelEnumInterfaceCache[$class];
    }

    public static function isMapperEnumInterface(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        if (!array_key_exists($class, static::$mapperEnumInterfaceCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$mapperEnumInterfaceCache[$class] = $reflectionClass->implementsInterface(RestApiBundle\Mapping\Mapper\EnumInterface::class);
        }

        return static::$mapperEnumInterfaceCache[$class];
    }

    public static function isResponseModelDateInterface(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        if (!array_key_exists($class, static::$responseModelDateInterfaceCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$responseModelDateInterfaceCache[$class] = $reflectionClass->implementsInterface(RestApiBundle\Mapping\ResponseModel\DateInterface::class);
        }

        return static::$responseModelDateInterfaceCache[$class];
    }

    public static function isMapperModelInterface(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        if (!array_key_exists($class, static::$mapperModelInterfaceCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$mapperModelInterfaceCache[$class] = $reflectionClass->isInstantiable()
                && $reflectionClass->implementsInterface(RestApiBundle\Mapping\Mapper\ModelInterface::class);
        }

        return static::$mapperModelInterfaceCache[$class];
    }

    public static function isRequestModelInterface(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        if (!array_key_exists($class, static::$requestModelInterfaceCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$requestModelInterfaceCache[$class] = $reflectionClass->isInstantiable()
                && $reflectionClass->implementsInterface(RestApiBundle\Mapping\RequestModel\RequestModelInterface::class);
        }

        return static::$requestModelInterfaceCache[$class];
    }
}
