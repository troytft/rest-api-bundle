<?php

namespace RestApiBundle\Mapping\ResponseModel;

interface EnumInterface
{
    /**
     * @return int|string
     */
    public function getValue();
}
