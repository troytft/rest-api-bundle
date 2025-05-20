<?php

declare(strict_types=1);

namespace RestApiBundle\Exception\Mapper;

use RestApiBundle;

class StackedMappingException extends \Exception
{
    /**
     * @var RestApiBundle\Exception\Mapper\StackableMappingExceptionInterface[]
     */
    private array $exceptions;

    /**
     * @param RestApiBundle\Exception\Mapper\StackableMappingExceptionInterface[] $exceptions
     */
    public function __construct(array $exceptions)
    {
        parent::__construct();

        $this->exceptions = $exceptions;
    }

    /**
     * @return RestApiBundle\Exception\Mapper\StackableMappingExceptionInterface[]
     */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }
}
