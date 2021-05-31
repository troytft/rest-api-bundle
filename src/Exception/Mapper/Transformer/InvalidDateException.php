<?php

namespace RestApiBundle\Exception\Mapper\Transformer;

class InvalidDateException extends \Exception implements TransformerExceptionInterface
{
    private string $errorMessage;

    public function __construct(string $format)
    {
        parent::__construct();

        $this->errorMessage = $format;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
