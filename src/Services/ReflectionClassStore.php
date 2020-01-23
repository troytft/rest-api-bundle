<?php

namespace RestApiBundle\Services;

use RestApiBundle;
use function ltrim;

class ReflectionClassStore
{
    /**
     * @var array<string,\ReflectionClass>
     */
    private static $reflectionClassCache;

    private function __construct()
    {
    }

    public static function get(string $class): \ReflectionClass
    {
        $class = ltrim($class, '\\');

        if (!isset(static::$reflectionClassCache[$class])) {
            static::$reflectionClassCache[$class] = new \ReflectionClass($class);
        }

        return static::$reflectionClassCache[$class];
    }
}
