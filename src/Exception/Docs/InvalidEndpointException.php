<?php

namespace RestApiBundle\Exception\Docs;

use function sprintf;

class InvalidEndpointException extends \Exception
{
    public function __construct(string $error, string $controllerClass, string $actionName)
    {
        parent::__construct(sprintf('%s Controller: %s, Action: %s', $error, $controllerClass, $actionName));
    }
}
