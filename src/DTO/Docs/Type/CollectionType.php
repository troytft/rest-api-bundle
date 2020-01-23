<?php

namespace RestApiBundle\DTO\Docs\Type;

use RestApiBundle;

class CollectionType implements RestApiBundle\DTO\Docs\Type\TypeInterface
{
    /**
     * @var RestApiBundle\DTO\Docs\Type\TypeInterface
     */
    private $type;

    /**
     * @var bool
     */
    private $isNullable;

    public function __construct(RestApiBundle\DTO\Docs\Type\TypeInterface $type, bool $isNullable)
    {
        $this->type = $type;
        $this->isNullable = $isNullable;
    }

    public function getType(): RestApiBundle\DTO\Docs\Type\TypeInterface
    {
        return $this->type;
    }

    public function getIsNullable(): bool
    {
        return $this->isNullable;
    }
}
