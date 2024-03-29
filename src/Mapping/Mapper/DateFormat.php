<?php

namespace RestApiBundle\Mapping\Mapper;

use function is_array;
use function is_string;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class DateFormat implements PropertyOptionInterface
{
    private string $format;

    /**
     * @param array|string $options
     */
    public function __construct($options)
    {
        if (is_array($options) && isset($options['value'])) {
            $this->format = $options['value'];
        } elseif (is_string($options)) {
            $this->format = $options;
        } else {
            throw new \InvalidArgumentException();
        }
    }

    public function getFormat(): string
    {
        return $this->format;
    }
}
