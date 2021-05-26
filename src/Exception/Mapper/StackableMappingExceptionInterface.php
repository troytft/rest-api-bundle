<?php

namespace RestApiBundle\Exception\Mapper;

interface StackableMappingExceptionInterface extends \Throwable
{
    public function getPath(): array;
    public function getPathAsString(): string;
}
