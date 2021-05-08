<?php

namespace RestApiBundle\Model\OpenApi\Types;

use RestApiBundle;

class IntegerType implements RestApiBundle\Model\OpenApi\Types\ScalarInterface
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
