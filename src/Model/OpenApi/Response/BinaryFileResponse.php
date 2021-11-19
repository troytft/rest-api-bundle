<?php

namespace RestApiBundle\Model\OpenApi\Response;

use RestApiBundle;

class BinaryFileResponse implements RestApiBundle\Model\OpenApi\Response\ResponseInterface
{
    public function getNullable(): bool
    {
        return false;
    }
}
