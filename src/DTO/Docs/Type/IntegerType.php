<?php

namespace RestApiBundle\DTO\Docs\Type;

use RestApiBundle;

class IntegerType implements RestApiBundle\DTO\Docs\Type\TypeInterface, RestApiBundle\DTO\Docs\Type\ScalarInterface
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
