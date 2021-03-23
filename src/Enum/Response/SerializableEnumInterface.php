<?php

namespace RestApiBundle\Enum\Response;

interface SerializableEnumInterface
{
    /**
     * @return int|string
     */
    public function getValue();
}
