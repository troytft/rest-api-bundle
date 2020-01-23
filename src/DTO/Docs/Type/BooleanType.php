<?php

namespace RestApiBundle\DTO\Docs\Type;

use RestApiBundle;

class BooleanType implements RestApiBundle\DTO\Docs\Type\TypeInterface, RestApiBundle\DTO\Docs\Type\ScalarInterface
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
