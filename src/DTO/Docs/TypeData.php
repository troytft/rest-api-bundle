<?php

namespace RestApiBundle\DTO\Docs;

class TypeData
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
