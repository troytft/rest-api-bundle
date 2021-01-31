<?php

namespace RestApiBundle\DTO\Docs\Types;

use RestApiBundle;
use Symfony\Component\Validator\Constraint;
use function sprintf;

class ArrayType implements
    RestApiBundle\DTO\Docs\Types\TypeInterface,
    RestApiBundle\DTO\Docs\Types\ValidationAwareInterface,
    RestApiBundle\DTO\Docs\Types\DescriptionAwareInterface
{
    /**
     * @var RestApiBundle\DTO\Docs\Types\TypeInterface
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

    public function __construct(RestApiBundle\DTO\Docs\Types\TypeInterface $innerType, bool $nullable)
    {
        $this->innerType = $innerType;
        $this->nullable = $nullable;
    }

    public function getInnerType(): RestApiBundle\DTO\Docs\Types\TypeInterface
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

    public function setDescription(?string $description)
    {
        throw new \LogicException();
    }

    public function getDescription(): ?string
    {
        $innerType = $this->getInnerType();
        if ($this->innerType instanceof RestApiBundle\DTO\Docs\Types\DescriptionAwareInterface && $this->innerType->getDescription()) {
            return sprintf('Array of %s', $this->innerType->getDescription());
        }

        return null;
    }
}
