<?php

namespace RestApiBundle\DTO\Docs\Schema;

use RestApiBundle;

class ArrayOfClassesType implements RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
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
