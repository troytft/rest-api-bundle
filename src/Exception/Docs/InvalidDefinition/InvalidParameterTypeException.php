<?php

namespace RestApiBundle\Exception\Docs\InvalidDefinition;

use RestApiBundle;

class InvalidParameterTypeException extends RestApiBundle\Exception\Docs\InvalidDefinition\BaseException
{
    public function __construct()
    {
        parent::__construct('Unsupported function parameter type.');
    }
}
