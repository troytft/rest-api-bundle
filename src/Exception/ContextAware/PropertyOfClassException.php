<?php

namespace RestApiBundle\Exception\ContextAware;

use RestApiBundle;

use function sprintf;

class PropertyOfClassException extends \Exception implements RestApiBundle\Exception\ContextAware\ContextAwareExceptionInterface
{
    private string $messageWithContext;

    public function __construct(string $message, string $class, string $propertyName, ?\Throwable $previous = null)
    {
        parent::__construct($message, previous: $previous);

        $this->messageWithContext = sprintf('%s %s::$%s', $message, $class, $propertyName);
    }

    public function getMessageWithContext(): string
    {
        return $this->messageWithContext;
    }
}
