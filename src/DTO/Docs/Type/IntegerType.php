<?php

namespace RestApiBundle\DTO\Docs\Type;

use RestApiBundle;

class IntegerType implements RestApiBundle\DTO\Docs\Type\TypeInterface, RestApiBundle\DTO\Docs\Type\ScalarInterface
{
    /**
     * @var bool
     */
    private $isNullable;

    /**
     * @var string|null
     */
    private $format;

    public function __construct(bool $isNullable)
    {
        $this->isNullable = $isNullable;
    }

    public function getIsNullable(): bool
    {
        return $this->isNullable;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format)
    {
        $this->format = $format;

        return $this;
    }
}
