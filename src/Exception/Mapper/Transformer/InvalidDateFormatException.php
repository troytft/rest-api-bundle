<?php
declare(strict_types=1);

namespace RestApiBundle\Exception\Mapper\Transformer;

class InvalidDateFormatException extends \Exception implements TransformerExceptionInterface
{
    private string $format;

    public function __construct(string $format)
    {
        parent::__construct();

        $this->format = $format;
    }

    public function getFormat(): string
    {
        return $this->format;
    }
}
