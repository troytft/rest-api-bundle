<?php

namespace RestApiBundle\Model\OpenApi\Response;

use RestApiBundle;

class ResponseModel implements RestApiBundle\Model\OpenApi\Response\ResponseInterface
{
    private string $class;
    private bool $nullable;

    public function __construct(string $class, bool $nullable)
    {
        $this->class = $class;
        $this->nullable = $nullable;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getNullable(): bool
    {
        return $this->nullable;
    }
}
