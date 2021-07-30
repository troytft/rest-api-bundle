<?php

namespace RestApiBundle\Exception\OpenApi;

use function sprintf;

class PropertyOfClassException extends \Exception implements ContextAwareExceptionInterface
{
    private string $context;

    public function __construct(string $message, string $class, string $propertyName)
    {
        parent::__construct($message);

        $this->context = sprintf('Property "%s" of class "%s"', $propertyName, $class);
    }

    public function getContext(): string
    {
        return $this->context;
    }
}
