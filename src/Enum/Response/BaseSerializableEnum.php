<?php

namespace RestApiBundle\Enum\Response;

use RestApiBundle;

abstract class BaseSerializableEnum implements RestApiBundle\Enum\Response\SerializableEnumInterface
{
    /**
     * @var int|string
     */
    private $value;

    /**
     * @param int|string $value
     */
    final private function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param int|string $value
     *
     * @return static
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
