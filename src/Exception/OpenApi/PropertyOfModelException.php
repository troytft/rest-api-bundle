<?php

namespace RestApiBundle\Exception\OpenApi;

use function sprintf;

class PropertyOfModelException extends \Exception implements ContextAwareExceptionInterface
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
