<?php

namespace RestApiBundle\DTO\Docs\Schema;

use RestApiBundle;

class IntegerType implements RestApiBundle\DTO\Docs\Schema\TypeInterface, RestApiBundle\DTO\Docs\Schema\ScalarInterface
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
