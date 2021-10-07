<?php

namespace RestApiBundle\Exception\ContextAware;

use RestApiBundle;

use function sprintf;

class FunctionOfClassException extends \Exception implements RestApiBundle\Exception\ContextAware\ContextAwareExceptionInterface
{
    private string $messageWithContext;

    public function __construct(string $message, string $class, string $functionName)
    {
        parent::__construct($message);

        $this->messageWithContext = sprintf('%s %s->%s()', $message, $class, $functionName);
    }

    public function getMessageWithContext(): string
    {
        return $this->messageWithContext;
    }
}
