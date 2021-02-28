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
    private function __construct($value)
    {
        $this->value = $value;
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
