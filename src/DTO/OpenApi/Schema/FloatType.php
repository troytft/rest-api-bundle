<?php

namespace RestApiBundle\DTO\OpenApi\Schema;

use RestApiBundle;
use Symfony\Component\Validator\Constraint;

class FloatType implements
    RestApiBundle\DTO\OpenApi\Schema\SchemaTypeInterface,
    RestApiBundle\DTO\OpenApi\Schema\ScalarInterface,
    RestApiBundle\DTO\OpenApi\Schema\ValidationAwareInterface,
    RestApiBundle\DTO\OpenApi\Schema\DescriptionAwareInterface
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
