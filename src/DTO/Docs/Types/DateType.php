<?php

namespace RestApiBundle\DTO\Docs\Types;

use RestApiBundle;

class DateType implements RestApiBundle\DTO\Docs\Types\TypeInterface
{
    /**
     * @var bool
     */
    private $nullable;

    public function __construct(bool $nullable)
    {
        $this->nullable = $nullable;
    }

    public function getNullable(): bool
    {
        return $this->nullable;
    }
}
