<?php

namespace RestApiBundle\Exception\ContextAware;

use RestApiBundle;

use function sprintf;

class PropertyOfClassException extends \Exception implements RestApiBundle\Exception\ContextAware\ContextAwareExceptionInterface
{
    public function __construct(string $message, string $class, string $propertyName, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf('%s %s::$%s', $message, $class, $propertyName), previous: $previous);
    }
}
