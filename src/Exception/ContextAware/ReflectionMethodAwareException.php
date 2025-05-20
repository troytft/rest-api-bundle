<?php

declare(strict_types=1);

namespace RestApiBundle\Exception\ContextAware;

class ReflectionMethodAwareException extends \Exception implements ContextAwareExceptionInterface
{
    public function __construct(string $message, \ReflectionMethod $reflectionMethod)
    {
        parent::__construct(\sprintf('%s %s->%s()', $message, $reflectionMethod->class, $reflectionMethod->name));
    }
}
