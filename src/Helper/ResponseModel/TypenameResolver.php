<?php

namespace RestApiBundle\Helper\ResponseModel;

use function explode;
use function join;
use function sprintf;

class TypenameResolver
{
    public static function resolve(string $class): string
    {
        $parts = [];
        $hasResponseModelPart = false;

        foreach (explode('\\', $class) as $part) {
            if ($hasResponseModelPart) {
                $parts[] = $part;
            } elseif ($part === 'ResponseModel') {
                $hasResponseModelPart = true;
            }
        }

        if (!$hasResponseModelPart) {
            throw new \RuntimeException(sprintf('Response model "%s" must be in "ResponseModel" namespace', $class));
        }

        $typename = join('_', $parts);
        if (!$typename) {
            throw new \RuntimeException(sprintf('Response model "%s" must have typename', $class));
        }

        return $typename;
    }
}
