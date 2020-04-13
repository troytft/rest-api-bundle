<?php

namespace RestApiBundle\DTO\Docs\Schema;

use RestApiBundle;
use Symfony\Component\Validator\Constraint;

class ArrayType implements RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface, RestApiBundle\DTO\Docs\Schema\ValidationAwareInterface
{
    /**
     * @var RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
     */
    private $innerType;

    /**
     * @var bool
     */
    private $nullable;

    /**
     * @var Constraint[]
     */
    private $constraints = [];

    public function __construct(RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface $innerType, bool $nullable)
    {
        $this->innerType = $innerType;
        $this->nullable = $nullable;
    }

    public function getInnerType(): RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
    {
        return $this->innerType;
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
