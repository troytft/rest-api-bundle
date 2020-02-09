<?php

namespace RestApiBundle\Exception\Docs\InvalidDefinition;

use RestApiBundle;

class InvalidRouteRequirementsException extends RestApiBundle\Exception\Docs\InvalidDefinition\BaseException
{
    public function __construct()
    {
        parent::__construct('Route requirements contain parameter not defined in route path.');
    }
}
