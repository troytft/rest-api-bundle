<?php

namespace RestApiBundle\Exception\Mapper\Transformer;

class InvalidDateTimeException extends \Exception implements TransformerExceptionInterface
{
    /**
     * @var string
     */
    private $errorMessage;

    /**
     * @param string $format
     */
    public function __construct(string $format)
    {
        parent::__construct();

        $this->errorMessage = $format;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
