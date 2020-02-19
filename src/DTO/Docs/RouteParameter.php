<?php

namespace RestApiBundle\DTO\Docs;

use RestApiBundle;

class RouteParameter
{
    /**
     * @var string
     */
    private $parameterType;

    /**
     * @var string
     */
    private $name;

    /**
     * @var RestApiBundle\DTO\Docs\Schema\TypeInterface
     */
    private $type;

    /**
     * @var string|null
     */
    private $description;

    public function __construct(string $parameterType, string $name, RestApiBundle\DTO\Docs\Schema\TypeInterface $type)
    {
        $this->parameterType = $parameterType;
        $this->name = $name;
        $this->type = $type;
    }

    public function getParameterType(): string
    {
        return $this->parameterType;
    }

    public function setParameterType(string $parameterType)
    {
        $this->parameterType = $parameterType;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): Schema\TypeInterface
    {
        return $this->type;
    }

    public function setType(Schema\TypeInterface $type)
    {
        $this->type = $type;

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
