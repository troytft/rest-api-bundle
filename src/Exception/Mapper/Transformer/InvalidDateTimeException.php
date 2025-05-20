<?php

declare(strict_types=1);

namespace RestApiBundle\Exception\Mapper\Transformer;

class InvalidDateTimeException extends \Exception implements TransformerExceptionInterface
{
    /**
     * @var string
     */
    private $errorMessage;

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
