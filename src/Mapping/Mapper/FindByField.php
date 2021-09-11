<?php

namespace RestApiBundle\Mapping\Mapper;

use function is_array;
use function is_string;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class FindByField
{
    private string $field;

    /**
     * @param array|string $options
     */
    public function __construct($options)
    {
        if (is_array($options) && isset($options['value'])) {
            $this->field = $options['value'];
        } elseif (is_string($options)) {
            $this->field = $options;
        } else {
            throw new \InvalidArgumentException();
        }
    }

    public function getField(): string
    {
        return $this->field;
    }
}
