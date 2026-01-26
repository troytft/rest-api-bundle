<?php

declare(strict_types=1);

namespace RestApiBundle\Helper;

class TypenameResolver
{
    public static function resolve(string $class, string $pathPart): string
    {
        return self::resolveWithoutSuffix($class, $pathPart) . $pathPart;
    }

    public static function resolveWithoutSuffix(string $class, string $pathPart): string
    {
        $parts = [];
        $hasResponseModelPart = false;

        foreach (\explode('\\', $class) as $part) {
            if ($hasResponseModelPart) {
                $parts[] = $part;
            } elseif ($part === $pathPart) {
                $hasResponseModelPart = true;
            }
        }

        if (!$hasResponseModelPart) {
            throw new \RuntimeException(\sprintf('%s "%s" must be in "%s" namespace', $pathPart, $class, $pathPart));
        }

        $typename = \implode('.', $parts);
        if (!$typename) {
            throw new \RuntimeException(\sprintf('%s "%s" must have typename', $pathPart, $class));
        }

        return $typename;
    }
}
