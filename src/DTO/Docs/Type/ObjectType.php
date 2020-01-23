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
    private $isNullable;

    public function __construct(array $properties, bool $isNullable)
    {
        $this->properties = $properties;
        $this->isNullable = $isNullable;
    }

    /**
     * @return array<string, RestApiBundle\DTO\Docs\Type\TypeInterface>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getIsNullable(): bool
    {
        return $this->isNullable;
    }

    public function setIsNullable(bool $isNullable)
    {
        $this->isNullable = $isNullable;

        return $this;
    }
}
