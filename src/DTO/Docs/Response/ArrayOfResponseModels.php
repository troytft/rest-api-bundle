<?php

namespace RestApiBundle\DTO\Docs\Response;

use RestApiBundle;

class ArrayOfResponseModels implements RestApiBundle\DTO\Docs\Response\ResponseInterface
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var bool
     */
    private $isNullable;

    public function getClass(): string
    {
        return $this->class;
    }

    public function getIsNullable(): bool
    {
        return $this->isNullable;
    }
}
