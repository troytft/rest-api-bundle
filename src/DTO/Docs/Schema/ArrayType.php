<?php

namespace RestApiBundle\DTO\Docs\Schema;

use RestApiBundle;
use Symfony\Component\Validator\Constraint;
use function sprintf;

class ArrayType implements
    RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface,
    RestApiBundle\DTO\Docs\Schema\ValidationAwareInterface,
    RestApiBundle\DTO\Docs\Schema\DescriptionAwareInterface
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

    public function setDescription(?string $description)
    {
        throw new \LogicException();
    }

    public function getDescription(): ?string
    {
        $innerType = $this->getInnerType();
        if ($this->innerType instanceof RestApiBundle\DTO\Docs\Schema\DescriptionAwareInterface && $this->innerType->getDescription()) {
            return sprintf('Array of %s', $this->innerType->getDescription());
        }

        return null;
    }
}
