<?php

namespace RestApiBundle\DTO\Docs\Schema;

use RestApiBundle;
use Symfony\Component\Validator\Constraint;

class ArrayOfClassesType implements RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface, RestApiBundle\DTO\Docs\Schema\ValidationAwareInterface
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var bool
     */
    private $nullable;

    /**
     * @var Constraint[]
     */
    private $constraints = [];

    public function __construct(string $class, bool $nullable)
    {
        $this->class = $class;
        $this->nullable = $nullable;
    }

    public function getClass(): string
    {
        return $this->class;
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
