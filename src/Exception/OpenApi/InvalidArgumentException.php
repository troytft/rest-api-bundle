<?php

namespace RestApiBundle\Exception\OpenApi;

class InvalidArgumentException extends \Exception implements ContextAwareExceptionInterface
{
    private string $context;

    public function __construct(string $message, string $context)
    {
        parent::__construct($message);

        $this->context = $context;
    }

    public function getContext(): string
    {
        return $this->context;
    }
}
