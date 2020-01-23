<?php

namespace RestApiBundle\DTO\Docs\ReturnType;

use RestApiBundle;

class ClassType implements RestApiBundle\DTO\Docs\ReturnType\ReturnTypeInterface
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var bool
     */
    private $isNullPossible;

    public function __construct(string $class, bool $isNullPossible)
    {
        $this->class = $class;
        $this->isNullPossible = $isNullPossible;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getIsNullPossible(): bool
    {
        return $this->isNullPossible;
    }
}
