<?php

namespace RestApiBundle\Model\Mapper;

class Context
{
    public function __construct(public bool $clearMissing = true)
    {
    }
}
