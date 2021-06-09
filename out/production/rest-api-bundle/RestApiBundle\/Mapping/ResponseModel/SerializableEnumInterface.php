<?php

namespace RestApiBundle\Mapping\ResponseModel;

interface SerializableEnumInterface
{
    /**
     * @return int|string
     */
    public function getValue();
}
