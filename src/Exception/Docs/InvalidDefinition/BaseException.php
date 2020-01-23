<?php

namespace RestApiBundle\Exception\Docs\InvalidDefinition;

use RestApiBundle;

class BaseException extends \Exception implements RestApiBundle\Exception\Docs\InvalidDefinition\InvalidDefinitionExceptionInterface
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
