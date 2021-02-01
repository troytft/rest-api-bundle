<?php

namespace RestApiBundle\DTO\Docs\Types;

use RestApiBundle;

class StringType implements RestApiBundle\DTO\Docs\Types\ScalarInterface
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
