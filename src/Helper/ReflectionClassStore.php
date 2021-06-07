<?php

namespace RestApiBundle\Helper;

use function ltrim;

class ReflectionClassStore
{
    /**
     * @var array<string,\ReflectionClass>
     */
    private static array $reflectionClassCache;

    private function __construct()
    {
    }

    public static function get(string $class): \ReflectionClass
    {
        if (!isset(static::$reflectionClassCache[$class])) {
            static::$reflectionClassCache[$class] = new \ReflectionClass($class);
        }

        return static::$reflectionClassCache[$class];
    }
}
