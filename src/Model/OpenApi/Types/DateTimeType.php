<?php

namespace RestApiBundle\Model\OpenApi\Types;

use RestApiBundle;

class DateTimeType implements RestApiBundle\Model\OpenApi\Types\TypeInterface
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
