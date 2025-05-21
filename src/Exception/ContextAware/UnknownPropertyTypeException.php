<?php

namespace RestApiBundle\Exception\ContextAware;

class UnknownPropertyTypeException extends PropertyAwareException
{
    public function __construct(string $class, string $propertyName, ?\Throwable $previous = null)
    {
        parent::__construct('Unknown property type', $class, $propertyName, $previous);
    }
}
