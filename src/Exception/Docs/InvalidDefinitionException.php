<?php

namespace RestApiBundle\Exception\Docs;

use function sprintf;

class InvalidDefinitionException extends \Exception
{
    /**
     * @var string
     */
    private $originalErrorMessage;

    /**
     * @var string
     */
    private $controllerClass;

    /**
     * @var string
     */
    private $actionName;

    public function __construct(string $originalErrorMessage, string $controllerClass, string $actionName)
    {
        $this->originalErrorMessage = $originalErrorMessage;
        $this->controllerClass = $controllerClass;
        $this->actionName = $actionName;

        parent::__construct(sprintf(
            'Original Error Message: %s, Controller: %s, Action: %s',
            $this->originalErrorMessage,
            $this->controllerClass,
            $this->actionName
        ));
    }

    public function getOriginalErrorMessage(): string
    {
        return $this->originalErrorMessage;
    }

    public function getControllerClass(): string
    {
        return $this->controllerClass;
    }

    public function getActionName(): string
    {
        return $this->actionName;
    }
}
