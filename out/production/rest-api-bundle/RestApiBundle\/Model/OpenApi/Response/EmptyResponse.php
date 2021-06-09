<?php

namespace RestApiBundle\Model\OpenApi\Response;

use RestApiBundle;

class EmptyResponse implements RestApiBundle\Model\OpenApi\Response\ResponseInterface
{
    public function getNullable(): bool
    {
        return true;
    }
}
