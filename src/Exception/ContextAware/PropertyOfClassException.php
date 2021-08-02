<?php

namespace RestApiBundle\Exception\ContextAware;

use RestApiBundle;

use function sprintf;

class PropertyOfClassException extends \Exception implements RestApiBundle\Exception\ContextAware\ContextAwareExceptionInterface
{
    private string $context;

    public function __construct(string $message, string $class, string $propertyName, ?\Throwable $previous = null)
    {
        parent::__construct($message, previous: $previous);

        $this->context = sprintf('%s->%s', $class, $propertyName);
    }

    public function getContext(): string
    {
        return $this->context;
    }
}
