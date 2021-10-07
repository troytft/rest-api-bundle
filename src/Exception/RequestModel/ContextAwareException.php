<?php

namespace RestApiBundle\Exception\RequestModel;

use Throwable;

class ContextAwareException extends \RuntimeException
{
    public function __construct(string $message, Throwable $previous)
    {
        parent::__construct($message, 0, $previous);
    }
}
