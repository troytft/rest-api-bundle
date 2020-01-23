<?php

namespace RestApiBundle\Exception\Docs;

class ValidationException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
