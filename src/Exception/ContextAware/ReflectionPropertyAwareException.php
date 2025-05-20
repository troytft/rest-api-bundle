<?php

declare(strict_types=1);

namespace RestApiBundle\Exception\ContextAware;

class ReflectionPropertyAwareException extends \Exception implements ContextAwareExceptionInterface
{
    public function __construct(string $message, \ReflectionProperty $reflectionProperty, ?\Throwable $previous = null)
    {
        parent::__construct(\sprintf('%s %s::$%s', $message, $reflectionProperty->class, $reflectionProperty->name), previous: $previous);
    }
}
