<?php

declare(strict_types=1);

namespace RestApiBundle\Exception\Mapper;

class StackedMappingException extends \Exception
{
    /**
     * @var StackableMappingExceptionInterface[]
     */
    private array $exceptions;

    /**
     * @param StackableMappingExceptionInterface[] $exceptions
     */
    public function __construct(array $exceptions)
    {
        parent::__construct();

        $this->exceptions = $exceptions;
    }

    /**
     * @return StackableMappingExceptionInterface[]
     */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }
}
