<?php

namespace RestApiBundle\Exception\Docs\InvalidDefinition;

use RestApiBundle;

class InvalidPathParametersException extends RestApiBundle\Exception\Docs\InvalidDefinition\BaseException
{
    public function __construct()
    {
        parent::__construct('Route requirements count is not the same as route path parameters count.');
    }
}
