<?php

namespace RestApiBundle\Helper;

use RestApiBundle;
use function array_key_exists;
use function class_exists;
use function sprintf;

class ClassHelper
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

    public static function isResponseModel(string $class): bool
    {
        static::assertClassExists($class);

        if (!array_key_exists($class, static::$responseModelCache)) {
            $reflectionClass = RestApiBundle\Services\ReflectionClassStore::get($class);

            static::$responseModelCache[$class] = $reflectionClass->isInstantiable()
                && $reflectionClass->implementsInterface(RestApiBundle\ResponseModelInterface::class);
        }

        return static::$responseModelCache[$class];
    }

    public static function isDateTime(string $class): bool
    {
        static::assertClassExists($class);

        if (!array_key_exists($class, static::$dateTimeCache)) {
            $reflectionClass = RestApiBundle\Services\ReflectionClassStore::get($class);

            static::$dateTimeCache[$class] = $reflectionClass->isInstantiable()
                && $reflectionClass->implementsInterface(\DateTime::class);
        }

        return static::$dateTimeCache[$class];
    }

    private static function assertClassExists(string $class): void
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class %s does not exist.', $class));
        }
    }
}
