<?php

namespace RestApiBundle\DTO\Docs\Type;

use RestApiBundle;

class ClassType implements RestApiBundle\DTO\Docs\Type\TypeInterface
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var bool
     */
    private $isNullable;

    public function __construct(string $class, bool $isNullable)
    {
        $this->class = $class;
        $this->isNullable = $isNullable;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getIsNullable(): bool
    {
        return $this->isNullable;
    }
}
