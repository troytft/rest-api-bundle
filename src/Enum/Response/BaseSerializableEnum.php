<?php

namespace RestApiBundle\Enum\Response;

use RestApiBundle;

use function is_int;
use function is_string;

abstract class BaseSerializableEnum implements RestApiBundle\Enum\Response\SerializableEnumInterface
{
    /**
     * @var int|string
     */
    private $value;

    /**
     * @param int|string $value
     */
    private function __construct($value)
    {
        if (!is_int($value) || !is_string($value)) {
            throw new \InvalidArgumentException();
        }
    }

    /**
     * @param int|string $value
     */
    public static function from($value)
    {
        return new static($value);
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritDoc}
     */
    abstract public function getValues(): array;
}
