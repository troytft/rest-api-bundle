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
    private static array $mapperDateCache = [
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
    private static array $responseModelDateCache = [];

    /**
     * @var array<string, bool>
     */
    private static array $mapperModelCache = [];

    /**
     * @var array<string, bool>
     */
    private static array $requestModelCache = [];

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

    public static function isMapperDate(string $class): bool
    {
        if (!array_key_exists($class, static::$mapperDateCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$mapperDateCache[$class] = $reflectionClass->isInstantiable() && $reflectionClass->implementsInterface(RestApiBundle\Mapping\Mapper\DateInterface::class);
        }

        return static::$mapperDateCache[$class];
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

    public static function isResponseModelDate(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        if (!array_key_exists($class, static::$responseModelDateCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$responseModelDateCache[$class] = $reflectionClass->implementsInterface(RestApiBundle\Mapping\ResponseModel\DateInterface::class);
        }

        return static::$responseModelDateCache[$class];
    }

    public static function isMapperModel(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        if (!array_key_exists($class, static::$mapperModelCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$mapperModelCache[$class] = $reflectionClass->isInstantiable()
                && $reflectionClass->implementsInterface(RestApiBundle\Mapping\Mapper\ModelInterface::class);
        }

        return static::$mapperModelCache[$class];
    }

    public static function isRequestModel(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        if (!array_key_exists($class, static::$requestModelCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$requestModelCache[$class] = $reflectionClass->isInstantiable()
                && $reflectionClass->implementsInterface(RestApiBundle\Mapping\RequestModel\RequestModelInterface::class);
        }

        return static::$requestModelCache[$class];
    }
}
