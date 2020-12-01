<?php

namespace RestApiBundle\DTO\OpenApi\Schema;

use RestApiBundle;

class ClassType implements RestApiBundle\DTO\OpenApi\Schema\TypeInterface
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
