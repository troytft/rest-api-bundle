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

    public function __construct(\Throwable $previousException, string $controllerClass, string $actionName)
    {
        $this->originalErrorMessage = $previousException;
        $this->controllerClass = $controllerClass;
        $this->actionName = $actionName;

        parent::__construct(sprintf(
            'Original Error Message: %s, Controller: %s, Action: %s',
            $this->originalErrorMessage,
            $this->controllerClass,
            $this->actionName
        ), 0, $previousException);
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
