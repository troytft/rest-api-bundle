<?php

namespace RestApiBundle\DTO\Docs;

use RestApiBundle;

class ActionParameter
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var RestApiBundle\DTO\Docs\Type\TypeInterface|null
     */
    private $type;

    public function __construct(string $name, ?RestApiBundle\DTO\Docs\Type\TypeInterface $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ?RestApiBundle\DTO\Docs\Type\TypeInterface
    {
        return $this->type;
    }
}
