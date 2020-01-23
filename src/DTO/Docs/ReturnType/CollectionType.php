<?php

namespace RestApiBundle\DTO\Docs\ReturnType;

use RestApiBundle;

class CollectionType implements RestApiBundle\DTO\Docs\ReturnType\ReturnTypeInterface
{
    /**
     * @var RestApiBundle\DTO\Docs\ReturnType\ReturnTypeInterface
     */
    private $type;

    /**
     * @var bool
     */
    private $isNullable;

    public function __construct(RestApiBundle\DTO\Docs\ReturnType\ReturnTypeInterface $type, bool $isNullable)
    {
        $this->type = $type;
        $this->isNullable = $isNullable;
    }

    public function getType(): RestApiBundle\DTO\Docs\ReturnType\ReturnTypeInterface
    {
        return $this->type;
    }

    public function getIsNullable(): bool
    {
        return $this->isNullable;
    }
}
