<?php

namespace RestApiBundle\Exception\Docs\InvalidDefinition;

use RestApiBundle;

class InvalidPathParametersException extends RestApiBundle\Exception\Docs\InvalidDefinition\BaseException
{
    public function __construct()
    {
        parent::__construct('Route path parameters do not match parameters from route requirements.');
    }
}
