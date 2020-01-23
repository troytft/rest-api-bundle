<?php

namespace RestApiBundle\DTO\Docs\Type;

use RestApiBundle;

class FloatType implements RestApiBundle\DTO\Docs\Type\TypeInterface
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
