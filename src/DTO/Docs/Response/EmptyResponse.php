<?php

namespace RestApiBundle\DTO\Docs\Response;

use RestApiBundle;

class EmptyResponse implements RestApiBundle\DTO\Docs\Response\ResponseInterface
{
    public function getNullable(): bool
    {
        return true;
    }
}
