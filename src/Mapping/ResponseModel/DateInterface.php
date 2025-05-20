<?php
declare(strict_types=1);

namespace RestApiBundle\Mapping\ResponseModel;

interface DateInterface
{
    public function getValue(): \DateTimeInterface;
}
