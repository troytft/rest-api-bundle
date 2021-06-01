<?php

namespace RestApiBundle\Exception\Mapper;

class UndefinedTransformerException extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
