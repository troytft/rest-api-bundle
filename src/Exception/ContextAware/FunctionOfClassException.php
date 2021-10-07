<?php

namespace RestApiBundle\Exception\ContextAware;

use RestApiBundle;

use function sprintf;

class FunctionOfClassException extends \Exception implements RestApiBundle\Exception\ContextAware\ContextAwareExceptionInterface
{
    public function __construct(string $message, string $class, string $functionName)
    {
        parent::__construct(sprintf('%s %s->%s()', $message, $class, $functionName));
    }
}
