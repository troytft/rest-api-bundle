<?php

namespace RestApiBundle\Helper;

use function explode;
use function join;
use function sprintf;

class TypenameResolver
{
    private static function resolve(string $class, string $requiredNamespacePart): string
    {
        $parts = [];
        $hasRequiredPart = false;

        foreach (explode('\\', $class) as $part) {
            if ($hasRequiredPart) {
                $parts[] = $part;
            } elseif ($part === $requiredNamespacePart) {
                $hasRequiredPart = true;
            }
        }

        if (!$hasRequiredPart) {
            throw new \RuntimeException(sprintf('Class "%s" must be located in "%s" namespace', $class, $requiredNamespacePart));
        }

        $typename = join('_', $parts);
        if (!$typename) {
            throw new \LogicException();
        }

        return $typename . $requiredNamespacePart;
    }

    public static function resolveForResponseModel(string $class): string
    {
        return static::resolve($class, 'ResponseModel');
    }
}
