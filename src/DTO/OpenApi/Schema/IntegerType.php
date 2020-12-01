<?php

namespace RestApiBundle\DTO\OpenApi\Schema;

use RestApiBundle;
use Symfony\Component\Validator\Constraint;

class IntegerType implements RestApiBundle\DTO\OpenApi\Schema\TypeInterface
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
