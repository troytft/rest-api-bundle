<?php

namespace RestApiBundle\Helper;

use RestApiBundle;

use function array_key_exists;
use function class_exists;

class ClassInstanceHelper
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
        RestApiBundle\Mapping\Mapper\DateInterface::class => true,
    ];

    /**
     * @var array<string, bool>
     */
    private static array $timestampCache = [
        RestApiBundle\Mapping\Mapper\TimestampInterface::class => true,
    ];

    /**
     * @var array<string, bool>
     */
    private static array $serializableEnumCache = [];

    /**
     * @var array<string, bool>
     */
    private static array $serializableDateCache = [];

    /**
     * @var array<string, bool>
     */
    private static array $mapperModelCache = [];

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

    public static function isDate(string $class): bool
    {
        if (!array_key_exists($class, static::$dateCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$dateCache[$class] = $reflectionClass->implementsInterface(RestApiBundle\Mapping\Mapper\DateInterface::class);
        }

        return static::$dateCache[$class];
    }

    public static function isTimestamp(string $class): bool
    {
        if (!array_key_exists($class, static::$timeastampCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$timestampCache[$class] = $reflectionClass->implementsInterface(RestApiBundle\Mapping\Mapper\TypeInterface::class);
        }

        return static::$timestampCache[$class];
    }

    public static function isSerializableEnum(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        if (!array_key_exists($class, static::$serializableEnumCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$serializableEnumCache[$class] = $reflectionClass->implementsInterface(RestApiBundle\Mapping\ResponseModel\SerializableEnumInterface::class);
        }

        return static::$serializableEnumCache[$class];
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
}
