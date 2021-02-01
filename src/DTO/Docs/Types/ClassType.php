<?php

namespace RestApiBundle\DTO\Docs\Types;

use RestApiBundle;

class ClassType implements RestApiBundle\DTO\Docs\Types\TypeInterface
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var bool
     */
    private $nullable;

    public function __construct(string $class, bool $nullable)
    {
        $this->class = $class;
        $this->nullable = $nullable;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getNullable(): bool
    {
        return $this->nullable;
    }
}
