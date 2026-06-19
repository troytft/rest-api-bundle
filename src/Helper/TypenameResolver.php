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
        $allParts = \explode('\\', $class);

        $parts = [];
        $hasPathPart = false;

        foreach ($allParts as $part) {
            if ($hasPathPart) {
                $parts[] = $part;
            } elseif ($part === $pathPart) {
                $hasPathPart = true;
            }
        }

        if (!$hasPathPart) {
            $parts = $allParts;
        }

        $typename = \implode('.', $parts);
        if (!$typename) {
            throw new \RuntimeException(\sprintf('%s "%s" must have typename', $pathPart, $class));
        }

        return $typename;
    }
}
