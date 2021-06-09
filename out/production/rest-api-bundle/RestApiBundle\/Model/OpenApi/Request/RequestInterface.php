<?php

namespace RestApiBundle\Model\OpenApi\Request;

interface RequestInterface
{
    public function getNullable(): bool;
}
