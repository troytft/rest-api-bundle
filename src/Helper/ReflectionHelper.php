<?php

declare(strict_types=1);

namespace RestApiBundle\Helper;

use RestApiBundle;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ReflectionHelper
{
    /**
     * @var array<string,\ReflectionClass>
     */
    private static array $reflectionClassCache;

    public static function getReflectionClass(string $class): \ReflectionClass
    {
        if (!isset(static::$reflectionClassCache[$class])) {
            static::$reflectionClassCache[$class] = new \ReflectionClass($class);
        }

        return static::$reflectionClassCache[$class];
    }

    public static function isResponseModel(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        $reflectionClass = static::getReflectionClass($class);

        return $reflectionClass->isInstantiable() && $reflectionClass->implementsInterface(RestApiBundle\Mapping\ResponseModel\ResponseModelInterface::class);
    }

    public static function isDateTime(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        $reflectionClass = static::getReflectionClass($class);

        return $reflectionClass->isInstantiable() && $reflectionClass->implementsInterface(\DateTimeInterface::class);
    }

    public static function isUploadedFile(string $class): bool
    {
        return $class === UploadedFile::class;
    }

    public static function isMapperDate(string $class): bool
    {
        $reflectionClass = static::getReflectionClass($class);

        return $reflectionClass->isInstantiable() && $reflectionClass->implementsInterface(RestApiBundle\Mapping\Mapper\DateInterface::class);
    }

    public static function isResponseModelEnum(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        $reflectionClass = static::getReflectionClass($class);

        return $reflectionClass->implementsInterface(RestApiBundle\Mapping\ResponseModel\EnumInterface::class);
    }

    public static function isMapperEnum(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        $reflectionClass = static::getReflectionClass($class);

        return $reflectionClass->implementsInterface(RestApiBundle\Mapping\Mapper\EnumInterface::class);
    }

    public static function isResponseModelDate(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        $reflectionClass = static::getReflectionClass($class);

        return $reflectionClass->implementsInterface(RestApiBundle\Mapping\ResponseModel\DateInterface::class);
    }

    public static function isMapperModel(string $class): bool
    {
        $reflectionClass = static::getReflectionClass($class);

        return $reflectionClass->isInstantiable() && $reflectionClass->implementsInterface(RestApiBundle\Mapping\Mapper\ModelInterface::class);
    }

    public static function isRequestModel(string $class): bool
    {
        $reflectionClass = static::getReflectionClass($class);

        return $reflectionClass->isInstantiable() && $reflectionClass->implementsInterface(RestApiBundle\Mapping\RequestModel\RequestModelInterface::class);
    }

    public static function isDeprecated(\ReflectionMethod|\ReflectionProperty $reflection): bool
    {
        return \is_string($reflection->getDocComment()) && str_contains($reflection->getDocComment(), '@deprecated');
    }
}
