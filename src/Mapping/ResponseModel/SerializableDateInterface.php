<?php

namespace RestApiBundle\Mapping\ResponseModel;

interface SerializableDateInterface
{
    public function getValue(): \DateTimeInterface;
}
