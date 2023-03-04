<?php

namespace RestApiBundle\Mapping\ResponseModel;

/**
 * @property int|string $value
 */
interface EnumInterface
{
    /**
     * @return int|string
     */
    public function getValue();
}
