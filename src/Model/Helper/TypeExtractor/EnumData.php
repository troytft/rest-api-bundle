<?php

namespace RestApiBundle\Model\Helper\TypeExtractor;

class EnumData
{
    /**
     * @param string[]|int[]|float[] $values
     */
    public function __construct(
        public string $type,
        public array $values,
    ) {
    }
}
