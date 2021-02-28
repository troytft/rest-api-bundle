<?php

namespace RestApiBundle\Enum\Response;

interface SerializableEnumInterface
{
    /**
     * @return string[]|int[]
     */
    public function getValues(): array;

    /**
     * @return int|string
     */
    public function getValue();
}
