<?php

namespace RestApiBundle\Exception\ContextAware;

use RestApiBundle;

use function sprintf;

class FunctionOfClass extends \Exception implements RestApiBundle\Exception\ContextAware\ContextAwareExceptionInterface
{
    private string $context;

    public function __construct(string $message, string $class, string $functionName)
    {
        parent::__construct($message);

        $this->context = sprintf('%s->%s()', $class, $functionName);
    }

    public function getContext(): string
    {
        return $this->context;
    }
}
