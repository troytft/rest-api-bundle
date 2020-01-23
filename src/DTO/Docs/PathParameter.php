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
     * @var string|null
     */
    private $format;

    public function __construct(string $name, ?RestApiBundle\DTO\Docs\Type\TypeInterface $type, ?string $format)
    {
        $this->name = $name;
        $this->type = $type;
        $this->format = $format;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ?Type\TypeInterface
    {
        return $this->type;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }
}
