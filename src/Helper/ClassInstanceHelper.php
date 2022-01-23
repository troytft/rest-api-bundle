<?php

namespace RestApiBundle\Helper;

use RestApiBundle;

use function array_key_exists;
use function class_exists;

final class ClassInstanceHelper
{
    /**
     * @var array<string, bool>
     */
    private static array $responseModelCache = [];

    /**
     * @var array<string, bool>
     */
    private static array $dateTimeCache = [
        \DateTime::class => true,
    ];

    /**
     * @var array<string, bool>
     */
    private static array $dateCache = [
        RestApiBundle\Mapping\Mapper\Date::class => true,
    ];

    /**
     * @var array<string, bool>
     */
    private static array $responseModelEnumCache = [];

    /**
     * @var array<string, bool>
     */
    private static array $mapperEnumCache = [];

    /**
     * @var array<string, bool>
     */
    private static array $serializableDateCache = [];

    /**
     * @var array<string, bool>
     */
    private static array $mapperModelInterfaceCache = [];

    /**
     * @var array<string, bool>
     */
    private static array $requestModelInterfaceCache = [];

    public static function isResponseModel(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        if (!array_key_exists($class, static::$responseModelCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$responseModelCache[$class] = $reflectionClass->isInstantiable()
                && $reflectionClass->implementsInterface(RestApiBundle\Mapping\ResponseModel\ResponseModelInterface::class);
        }

        return static::$responseModelCache[$class];
    }

    public static function isDateTime(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        if (!array_key_exists($class, static::$dateTimeCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$dateTimeCache[$class] = $reflectionClass->isInstantiable()
                && $reflectionClass->implementsInterface(\DateTimeInterface::class);
        }

        return static::$dateTimeCache[$class];
    }

    public static function isDateMapperType(string $class): bool
    {
        if (!array_key_exists($class, static::$dateCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$dateCache[$class] = $reflectionClass->isInstantiable() && $reflectionClass->implementsInterface(RestApiBundle\Mapping\Mapper\DateInterface::class);
        }

        return static::$dateCache[$class];
    }

    public static function isResponseModelEnum(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        if (!array_key_exists($class, static::$responseModelEnumCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$responseModelEnumCache[$class] = $reflectionClass->implementsInterface(RestApiBundle\Mapping\ResponseModel\EnumInterface::class);
        }

        return static::$responseModelEnumCache[$class];
    }

    public static function isMapperEnum(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        if (!array_key_exists($class, static::$mapperEnumCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$mapperEnumCache[$class] = $reflectionClass->implementsInterface(RestApiBundle\Mapping\Mapper\EnumInterface::class);
        }

        return static::$mapperEnumCache[$class];
    }

    public static function isSerializableDate(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        if (!array_key_exists($class, static::$serializableDateCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$serializableDateCache[$class] = $reflectionClass->implementsInterface(RestApiBundle\Mapping\ResponseModel\SerializableDateInterface::class);
        }

        return static::$serializableDateCache[$class];
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
