<?php

namespace RestApiBundle\Exception\OpenApi;

class ClassPropertyException extends \Exception implements ContextAwareExceptionInterface
{
    private string $context;

    public function __construct(string $message, string $class, string $property)
    {
        parent::__construct($message);

        $this->context = $class . '->' . $property;
    }

    public function getContext(): string
    {
        return $this->context;
    }
}
