<?php

namespace RestApiBundle\DTO\OpenApi\Schema;

use RestApiBundle;
use Symfony\Component\Validator\Constraint;
use function sprintf;

class ArrayType implements
    RestApiBundle\DTO\OpenApi\Schema\SchemaTypeInterface,
    RestApiBundle\DTO\OpenApi\Schema\ValidationAwareInterface,
    RestApiBundle\DTO\OpenApi\Schema\DescriptionAwareInterface,
    RestApiBundle\DTO\OpenApi\ResponseInterface
{
    /**
     * @var RestApiBundle\DTO\OpenApi\Schema\SchemaTypeInterface
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

    public function __construct(RestApiBundle\DTO\OpenApi\Schema\SchemaTypeInterface $innerType, bool $nullable)
    {
        $this->innerType = $innerType;
        $this->nullable = $nullable;
    }

    public function getInnerType(): RestApiBundle\DTO\OpenApi\Schema\SchemaTypeInterface
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
        if ($this->innerType instanceof RestApiBundle\DTO\OpenApi\Schema\DescriptionAwareInterface && $this->innerType->getDescription()) {
            return sprintf('Array of %s', $this->innerType->getDescription());
        }

        return null;
    }
}
