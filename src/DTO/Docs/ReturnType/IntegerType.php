<?php

namespace RestApiBundle\DTO\Docs\ReturnType;

use RestApiBundle;

class IntegerType implements RestApiBundle\DTO\Docs\ReturnType\ReturnTypeInterface
{
    /**
     * @var bool
     */
    private $isNullable;

    public function __construct(bool $isNullable)
    {
        $this->isNullable = $isNullable;
    }

    public function getIsNullable(): bool
    {
        return $this->isNullable;
    }
}
