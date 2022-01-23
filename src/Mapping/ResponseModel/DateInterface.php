<?php

namespace RestApiBundle\Mapping\ResponseModel;

interface DateInterface
{
    public function getValue(): \DateTimeInterface;
}
