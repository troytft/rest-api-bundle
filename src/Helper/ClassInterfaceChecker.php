<?php

namespace RestApiBundle\Helper;

use RestApiBundle;

use function array_key_exists;
use function class_exists;

class ClassInterfaceChecker
{
    /**
     * @var array<string, bool>
     */
    private static $responseModelCache = [];

    /**
     * @var array<string, bool>
     */
    private static $dateTimeCache = [
        \DateTime::class => true,
    ];

    /**
     * @var array<string, bool>
     */
    private static $serializableEnumCache = [];

    /**
     * @var array<string, bool>
     */
    private static $requestModelCache = [];

    public static function isResponseModel(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        if (!array_key_exists($class, static::$responseModelCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$responseModelCache[$class] = $reflectionClass->isInstantiable()
                && $reflectionClass->implementsInterface(RestApiBundle\ResponseModelInterface::class);
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

    public static function isSerializableEnum(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        if (!array_key_exists($class, static::$serializableEnumCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$serializableEnumCache[$class] = $reflectionClass->implementsInterface(RestApiBundle\Enum\Response\SerializableEnumInterface::class);
        }

        return static::$serializableEnumCache[$class];
    }

    public static function isRequestModel(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        if (!array_key_exists($class, static::$requestModelCache)) {
            $reflectionClass = RestApiBundle\Helper\ReflectionClassStore::get($class);

            static::$requestModelCache[$class] = $reflectionClass->isInstantiable()
                && $reflectionClass->implementsInterface(RestApiBundle\RequestModelInterface::class);
        }

        return static::$requestModelCache[$class];
    }
}
