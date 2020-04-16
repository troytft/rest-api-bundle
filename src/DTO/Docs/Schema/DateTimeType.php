<?php

namespace RestApiBundle\DTO\Docs\Schema;

use RestApiBundle;
use Symfony\Component\Validator\Constraint;

class DateTimeType implements
    RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface,
    RestApiBundle\DTO\Docs\Schema\ValidationAwareInterface,
    RestApiBundle\DTO\Docs\Schema\DescriptionAwareInterface
{
    /**
     * @var bool
     */
    private $nullable;

    /**
     * @var Constraint[]
     */
    private $constraints = [];

    /**
     * @var string|null
     */
    private $description;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description)
    {
        $this->description = $description;

        return $this;
    }
}
