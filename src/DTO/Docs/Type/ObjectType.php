<?php

namespace RestApiBundle\DTO\Docs\Type;

use RestApiBundle;

class ObjectType implements RestApiBundle\DTO\Docs\Type\TypeInterface
{
    /**
     * @var array<string, RestApiBundle\DTO\Docs\Type\TypeInterface>
     */
    private $properties;

    /**
     * @var bool
     */
    private $nullable;

    public function __construct(array $properties, bool $nullable)
    {
        $this->properties = $properties;
        $this->nullable = $nullable;
    }

    /**
     * @return array<string, RestApiBundle\DTO\Docs\Type\TypeInterface>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getNullable(): bool
    {
        return $this->nullable;
    }

    public function setNullable(bool $nullable)
    {
        $this->nullable = $nullable;

        return $this;
    }
}
