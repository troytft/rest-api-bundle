<?php

namespace RestApiBundle\Services\Response;

use function explode;
use function join;
use function sprintf;

class TypenameResolver
{
    /**
     * @var array<string, string>
     */
    private $typenameCache = [];

    public function resolve(string $class): string
    {
        if (!isset($this->typenameCache[$class])) {
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
                throw new \RuntimeException(
                    sprintf('Response model "%s" must be in "ResponseModel" namespace', $class)
                );
            }

            $typename = join('_', $parts);
            if (!$typename) {
                throw new \RuntimeException(
                    sprintf('Response model "%s" must have typename', $class)
                );
            }

            $this->typenameCache[$class] = $typename;
        }

        return $this->typenameCache[$class];
    }
}
