<?php

declare(strict_types=1);

namespace RestApiBundle\Exception\ContextAware;

class PropertyAwareException extends \Exception implements ContextAwareExceptionInterface
{
    public function __construct(string $message, string $class, string $propertyName, ?\Throwable $previous = null)
    {
        parent::__construct(\sprintf('%s %s::$%s', $message, $class, $propertyName), previous: $previous);
    }
}
