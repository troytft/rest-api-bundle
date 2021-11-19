<?php

namespace RestApiBundle\Model\OpenApi\Response;

use RestApiBundle;

class RedirectResponse implements RestApiBundle\Model\OpenApi\Response\ResponseInterface
{
    private int $statusCode = 302;

    public function getNullable(): bool
    {
        return false;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): static
    {
        $this->statusCode = $statusCode;

        return $this;
    }
}
