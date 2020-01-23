<?php

namespace RestApiBundle\DTO\Docs\ReturnType;

use RestApiBundle;

class ObjectType implements RestApiBundle\DTO\Docs\ReturnType\ReturnTypeInterface
{
    /**
     * @var array<string, RestApiBundle\DTO\Docs\ReturnType\ReturnTypeInterface>
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
     * @return array<string, RestApiBundle\DTO\Docs\ReturnType\ReturnTypeInterface>
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
