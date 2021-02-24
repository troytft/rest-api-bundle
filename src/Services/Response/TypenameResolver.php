<?php

namespace RestApiBundle\Services\Response;

use function explode;
use function join;
use function sprintf;

class TypenameResolver
{
    public function resolve(string $class): string
    {
        $parts = [];
        $isResponseModel = false;

        foreach (explode('\\', $class) as $part) {
            if ($isResponseModel) {
                $parts[] = $part;
            } elseif ($part === 'ResponseModel') {
                $isResponseModel = true;
            }
        }

        if (!$isResponseModel) {
            throw new \RuntimeException(sprintf('Response model "%s" must be in "ResponseModel" namespace', $class));
        }

        if (!$parts) {
            throw new \RuntimeException();
        }

        return join('_', $parts);
    }
}
