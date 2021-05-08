<?php

namespace RestApiBundle\Mapping\ResponseModel;

class SerializableDate implements SerializableDateInterface
{
    private \DateTimeInterface $value;

    public function getValue(): \DateTimeInterface
    {
        return $this->value;
    }

    final private function __construct(\DateTimeInterface $value)
    {
        $this->value = $value;
    }

    /**
     * @return static
     */
    public static function from(\DateTimeInterface $value)
    {
        return new static($value);
    }
}
