<?php

namespace RestApiBundle\DTO\Docs;

use RestApiBundle;

class PathParameter
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var RestApiBundle\DTO\Docs\Type\TypeInterface|null
     */
    private $type;

    /**
     * @var string
     */
    private $description;

    public function __construct(string $name, ?RestApiBundle\DTO\Docs\Type\TypeInterface $type, string $description)
    {
        $this->name = $name;
        $this->type = $type;
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ?Type\TypeInterface
    {
        return $this->type;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
