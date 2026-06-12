<?php

declare(strict_types=1);

namespace RestApiBundle\Exception\Mapper;

interface StackableMappingExceptionInterface extends \Throwable
{
    /**
     * @return array<int|string>
     */
    public function getPath(): array;

    public function getPathAsString(): string;
}
