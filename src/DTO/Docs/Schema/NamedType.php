<?php

namespace RestApiBundle\DTO\Docs\Schema;

use RestApiBundle;

class NamedType implements RestApiBundle\DTO\Docs\Schema\TypeInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var RestApiBundle\DTO\Docs\Schema\TypeInterface|null
     */
    private $type;

    /**
     * @var bool
     */
    private $nullable;

    public function __construct(string $name, ?RestApiBundle\DTO\Docs\Schema\TypeInterface $type, bool $nullable)
    {
        $this->name = $name;
        $this->type = $type;
        $this->nullable = $nullable;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ?RestApiBundle\DTO\Docs\Schema\TypeInterface
    {
        return $this->type;
    }

    public function getNullable(): bool
    {
        return $this->nullable;
    }
}
