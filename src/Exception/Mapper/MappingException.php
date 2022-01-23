<?php

namespace RestApiBundle\Exception\Mapper;

use RestApiBundle;

class MappingException extends \RuntimeException implements RestApiBundle\Exception\ExceptionInterface
{
    /**
     * @param array<string, string[]> $errors
     */
    public function __construct(private array $errors)
    {
        parent::__construct();
    }

    /**
     * @return array<string, string[]>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
