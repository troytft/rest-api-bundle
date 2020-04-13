<?php

namespace RestApiBundle\DTO\Docs\Schema;

use Symfony\Component\Validator\Constraint;
use RestApiBundle;

class FloatType implements RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface, RestApiBundle\DTO\Docs\Schema\ScalarInterface
{
    /**
     * @var bool
     */
    private $nullable;

    /**
     * @var Constraint[]
     */
    private $constraints = [];

    public function __construct(bool $nullable)
    {
        $this->nullable = $nullable;
    }

    public function getNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * @return Constraint[]
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    /**
     * @param Constraint[] $constraints
     *
     * @return $this
     */
    public function setConstraints(array $constraints)
    {
        $this->constraints = $constraints;

        return $this;
    }
}
