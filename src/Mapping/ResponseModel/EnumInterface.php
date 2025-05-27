<?php

declare(strict_types=1);

namespace RestApiBundle\Mapping\ResponseModel;

interface EnumInterface
{
    public function getValue(): int|string;
}
