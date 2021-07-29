<?php

namespace RestApiBundle\Exception\OpenApi;

use function sprintf;

class ActionOfControllerException extends \Exception implements ContextAwareExceptionInterface
{
    private string $context;

    public function __construct(string $message, string $controller, string $action)
    {
        parent::__construct($message);

        $this->context = sprintf('Action "%s" of controller "%s"', $action, $controller);
    }

    public function getContext(): string
    {
        return $this->context;
    }
}
