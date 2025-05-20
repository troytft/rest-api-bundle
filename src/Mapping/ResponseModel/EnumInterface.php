<?php
declare(strict_types=1);

namespace RestApiBundle\Mapping\ResponseModel;

interface EnumInterface
{
    /**
     * @return int|string
     */
    public function getValue();
}
