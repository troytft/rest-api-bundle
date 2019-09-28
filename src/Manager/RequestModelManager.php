<?php

namespace RestApiBundle\Manager;

use RestApiBundle\RequestModelInterface;

class RequestModelManager
{
    public function handleRequest(RequestModelInterface $requestModel, array $data): void
    {
        throw new \RuntimeException('Not implemented');
    }
}
